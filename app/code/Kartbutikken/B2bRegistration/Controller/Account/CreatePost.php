<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\B2bRegistration\Controller\Account;

use Bss\B2bRegistration\Helper\CreateAccount;
use Bss\B2bRegistration\Helper\CreatePostHelper;
use Bss\B2bRegistration\Helper\Data;
use Exception;
use Hryvinskyi\Base\Helper\ArrayHelper;
use Hryvinskyi\Base\Helper\VarDumper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Metadata\Form;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Registry;
use Magento\Customer\Api\CustomerRepositoryInterface;
/**
 * Class CreatePost
 */
class CreatePost extends AbstractAccount
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var CreateAccount
     */
    private $helperCreateAccount;

    /**
     * @var CreatePostHelper
     */
    private $createPostHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Form
     */
    private $addressForm;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * CreatePost constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param CreateAccount $helperCreateAccount
     * @param CreatePostHelper $createPostHelper
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Data $helper,
        CreateAccount $helperCreateAccount,
        CreatePostHelper $createPostHelper,
        Registry $registry,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->helperCreateAccount = $helperCreateAccount;
        $this->createPostHelper = $createPostHelper;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Add address to customer during create account
     *
     * @return array
     */
    protected function extractAddresses()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $params = $this->getRequest()->getParams();
        $params['shipping-address']['firstname'] = $params['firstname'];
        $params['shipping-address']['lastname'] = $params['lastname'];
        $params['billing-address']['firstname'] = $params['firstname'];
        $params['billing-address']['lastname'] = $params['lastname'];

        if(ArrayHelper::getValue($params, 'is_same_as_billing')) {
            $params['shipping-address'] = $params['billing-address'];
        }

        $billingAddressDataObject = $this->extractAddress($params['billing-address']);
        $shippingAddressDataObject = $this->extractAddress($params['shipping-address']);
        $billingAddressDataObject->setIsDefaultBilling(true);
        $shippingAddressDataObject->setIsDefaultShipping(true);

        $return = [$billingAddressDataObject, $shippingAddressDataObject];

        if (ArrayHelper::getValue($params, 'is_same_as_billing')) {
            $billingAddressDataObject->setIsDefaultShipping(true);
            $return = [$billingAddressDataObject];
        }

        if (ArrayHelper::getValue($params, 'is_add_additional_address')) {
            $params['additional-address']['firstname'] = $params['firstname'];
            $params['additional-address']['lastname'] = $params['lastname'];
            $return[] = $this->extractAddress($params['additional-address']);
        }

        return $return;
    }

    /**
     * @return Form
     */
    private function getAddressForm(): Form
    {
        if ($this->addressForm === null) {
            $this->addressForm = $this->helperCreateAccount
                ->getFormFactory()
                ->create('customer_address', 'customer_register_address');
        }

        return $this->addressForm;
    }

    /**
     * @param array $params
     *
     * @return AddressInterface
     */
    private function extractAddress(array $params): AddressInterface
    {
        $allowedAttributes = $this->getAddressForm()->getAllowedAttributes();
        $addressData = [];
        $regionDataObject = $this->helperCreateAccount->getRegionDataFactory();

        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = ArrayHelper::getValue($params, $attributeCode);
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $regionDataObject->setRegionId($value);
                    break;
                case 'region':
                    $regionDataObject->setRegion($value);
                    break;
                default:
                    $addressData[$attributeCode] = $value;
            }
        }

        $addressDataObject = $this->helperCreateAccount->getDataAddressFactory();
        $this->helper->getDataObject()->populateWithArray($addressDataObject, $addressData, AddressInterface::class);
        $addressDataObject->setRegion($regionDataObject);

        return $addressDataObject;
    }

    /**
     * @return Session
     */
    protected function returnCustomerSession()
    {
        return $this->helperCreateAccount->getCustomerSessionFactory()->create();
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     *
     * @return void
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Create B2b account Action
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->checkLogin();

        if (!$this->getRequest()->isPost()
            || !$this->createPostHelper->returnValidator()->validate($this->getRequest())
        ) {
            $url = $this->createPostHelper->returnUrlFactory()->create()->getUrl('*/*/create', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->error($url));

            return $resultRedirect;
        }

        $autoApproval = $this->helper->isAutoApproval();
        $customerSession = $this->returnCustomerSession();
        $customerSession->regenerateId();

        try {
            $this->registry->register('isSecureArea', true);
            $addresses = $this->extractAddresses();
            $customer = $this->helper->getCustomerExtractor()
                ->extract(
                    $this->getFormExtract(),
                    $this->_request
                );
            $customer->setCustomAttribute('invoice_email', ArrayHelper::getValue($this->_request->getParams(), 'invoice_email'));
            $customer->setCustomAttribute('use_invoice_email', ArrayHelper::getValue($this->_request->getParams(), 'use_invoice_email'));
            $customer->setAddresses($addresses);
            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $customerSession->getBeforeAuthUrl();
            $this->checkPasswordConfirmation($password, $confirmation);
            $customer = $this->createPostHelper->returnAccountManagement()
                ->createAccount($customer, $password, $redirectUrl);
            $customerSession->setBssSaveAccount('true');
            $this->subcribeCustomer($customer);
            $this->saveGroupAttribute($customer);

            $this->registry->unregister('isSecureArea');
            $this->_eventManager->dispatch(
                'bss_customer_register_success',
                ['account_controller' => $this, 'customer' => $customer]
            );

            $resultRedirect = $this->setCustomerStatus($customer, $autoApproval, $resultRedirect);

            return $resultRedirect;
        } catch (StateException $e) {
            $url = $this->createPostHelper->returnUrlFactory()->create()->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t save the customer.'));
        }

        $customerSession->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->createPostHelper
            ->returnUrlFactory()
            ->create()
            ->getUrl('btwob/account/create', ['_secure' => true]);
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));

        return $resultRedirect;
    }

    /**
     * @return string
     */
    protected function getFormExtract()
    {
        return 'customer_account_create';
    }

    /**
     * @param object $customer
     *
     * @return void
     */
    protected function setCustomerStatusConfirm($customer, $autoApproval)
    {
        if ($autoApproval) {
            $customer->setCustomAttribute("b2b_activasion_status", $this->createPostHelper->returnApproval());
        } else {
            $customer->setCustomAttribute("b2b_activasion_status", $this->createPostHelper->returnPending());
        }
    }

    /**
     * Check Customer Login
     * @return Redirect
     */
    protected function checkLogin()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->returnCustomerSession()->isLoggedIn()) {
            $resultRedirect->setPath('customer/account/index');

            return $resultRedirect;
        }
    }

    /**
     * Check subcribe customer
     *
     * @param object $customer
     *
     * @return void
     */
    protected function subcribeCustomer($customer)
    {
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $this->helperCreateAccount->getSubscriberFactory()->subscribeCustomerById($customer->getId());
        }
    }

    /**
     * Save B2b Customer Group
     *
     * @param CustomerInterface $customer
     *
     * @return void
     */
    protected function saveGroupAttribute($customer)
    {
        try {
            $customerGroupId = $this->helper->getCustomerGroup();
            $tax = $this->getRequest()->getPostValue('taxvat');
            $gender = $this->getRequest()->getPostValue('gender');

            if ($tax) {
                $customer->setTaxvat($tax);
            }
            if ($gender) {
                $customer->setGender($gender);
            }
            if (!$this->helper->isAutoAssigCustomerGroup()) {
                $customer->setGroupId($customerGroupId);
            }
            $this->customerRepository->save($customer);
        } catch (Exception $e) {
            $this->createPostHelper->returnLogger()->debug($e->getMessage());
        }
    }

    /**
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->helperCreateAccount->getAddressHelper()->isVatValidationEnabled()) {
            if ($this->helperCreateAccount->getAddressHelper()
                    ->getTaxCalculationAddressType() == $this->createPostHelper->returnTypeShipping()
            ) {
                // @codingStandardsIgnoreStart
                $message = sprintf(
                    'If you are a registered VAT customer, please <a href="%s">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->createPostHelper->returnUrlFactory()->create()->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $message = sprintf(
                    'If you are a registered VAT customer, please <a href="%s">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->createPostHelper->returnUrlFactory()->create()->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            }
        } else {
            $storeName = $this->helper->getStoreName();
            $message = sprintf('Thank you for registering with %s.', $storeName);
        }

        return $message;
    }

    /**
     * @param Customer $customer
     * @param bool $autoApproval
     * @param Redirect $resultRedirect
     *
     * @return Forward|Redirect|string
     */
    protected function setCustomerStatus($customer, $autoApproval, $resultRedirect)
    {
        $customerEmail = $customer->getEmail();
        $emailTemplate = $this->helper->getAdminEmailTemplate();
        $fromEmail = $this->helper->getAdminEmailSender();
        $recipient = $this->helper->getAdminEmail();
        $recipient = str_replace(' ', '', $recipient);
        $recipient = (explode(',', $recipient));
        $emailVar = [
            'varEmail' => $customerEmail,
        ];
        $storeId = $this->helper->getStoreId();
        $confirmationStatus = $this->createPostHelper
            ->returnAccountManagement()
            ->getConfirmationStatus($customer->getId());
        if ($confirmationStatus === $this->createPostHelper->returnConfirmRequire()) {
            $this->setCustomerStatusConfirm($customer, $autoApproval);
            $this->customerRepository->save($customer);
            $emailUrl = $this->helper->getEmailConfirmUrl($customer->getEmail());
            // @codingStandardsIgnoreStart
            $this->messageManager->addSuccess(
                __(
                    'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                    $emailUrl
                )
            );
            // @codingStandardsIgnoreEnd
            if ($this->helper->isEnableAdminEmail()) {
                $this->createPostHelper
                    ->returnBssHelperEmail()
                    ->sendEmail($fromEmail, $recipient, $emailTemplate, $storeId, $emailVar);
            }
            $url = $this->createPostHelper
                ->returnUrlFactory()
                ->create()
                ->getUrl('customer/account/login', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->success($url));

            return $resultRedirect;
        } elseif ($autoApproval) {
            $customer->setCustomAttribute("b2b_activasion_status", $this->createPostHelper->returnApproval());
            $this->customerRepository->save($customer);
            $this->returnCustomerSession()->setCustomerDataAsLoggedIn($customer);
            $this->messageManager->addSuccess(__($this->getSuccessMessage()));
            $resultRedirect = $this->callBackUrl($resultRedirect);

            return $resultRedirect;
        } else {
            $customer->setCustomAttribute("b2b_activasion_status", $this->createPostHelper->returnPending());
            $this->customerRepository->save($customer);
            $message = $this->helper->getPendingMess();
            $this->messageManager->addSuccess($message);
            if ($this->helper->isEnableAdminEmail()) {
                $this->createPostHelper
                    ->returnBssHelperEmail()
                    ->sendEmail($fromEmail, $recipient, $emailTemplate, $storeId, $emailVar);
            }
            $url = $this->createPostHelper
                ->returnUrlFactory()
                ->create()
                ->getUrl('customer/account/login', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->success($url));

            return $resultRedirect;
        }
    }

    /**
     * @param Redirect $resultRedirect
     *
     * @return Forward|Redirect
     */
    protected function callBackUrl($resultRedirect)
    {
        $requestedRedirect = $this->createPostHelper->returnAccountRedirect()->getRedirectCookie();
        if (!$this->helperCreateAccount->getScopeConfig()->getValue('customer/startup/redirect_dashboard') &&
            $requestedRedirect
        ) {
            $resultRedirect->setUrl($this->_redirect->success($requestedRedirect));
            $this->createPostHelper->returnAccountRedirect()->clearRedirectCookie();

            return $resultRedirect;
        }

        return $this->createPostHelper->returnAccountRedirect()->getRedirect();
    }
}
