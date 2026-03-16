<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Observer;

use Klarna\Base\Exception as KlarnaException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Newsletter\Model\Subscriber;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class MerchantCheckboxNewsletterSignup implements ObserverInterface
{

    /**
     * @var Url
     */
    private $url;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var ManagerInterface
     */
    private $messageMananger;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @param CustomerSession $customerSession
     * @param Url $url
     * @param ScopeConfigInterface $config
     * @param ManagerInterface $messageMananger
     * @param Subscriber $subscriber
     * @codeCoverageIgnore
     */
    public function __construct(
        CustomerSession $customerSession,
        Url $url,
        ScopeConfigInterface $config,
        ManagerInterface $messageMananger,
        Subscriber $subscriber
    ) {
        $this->customerSession = $customerSession;
        $this->url = $url;
        $this->config = $config;
        $this->messageMananger = $messageMananger;
        $this->subscriber = $subscriber;
    }

    /**
     * Signing up for the newsletter
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        if ($observer->getChecked() && ($email = ($quote->getCustomerEmail() ?: $quote->getCustomer()->getEmail()))) {
            try {
                if (!$this->config->isSetFlag(Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG)
                    && !$this->customerSession->isLoggedIn()
                ) {
                    throw new KlarnaException(__(
                        'Sorry, but administrator denied subscription for guests. Please <a href="%1">register</a>.',
                        $this->url->getRegisterUrl()
                    ));
                }

                $status = $this->subscriber->subscribe($email);
                if ($status === Subscriber::STATUS_NOT_ACTIVE) {
                    $this->messageMananger->addSuccess(__('Confirmation request has been sent.'));
                } else {
                    $this->messageMananger->addSuccess(__('Thank you for your subscription.'));
                }
            } catch (KlarnaException $e) {
                $this->messageMananger->addException(
                    $e,
                    __(
                        'There was a problem with the subscription: %1',
                        $e->getMessage()
                    )
                );
            } catch (\Exception $e) {
                $this->messageMananger->addException($e, __('There was a problem with the subscription.'));
            }
        }
    }
}
