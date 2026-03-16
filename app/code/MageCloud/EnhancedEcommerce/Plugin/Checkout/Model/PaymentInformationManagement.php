<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
namespace MageCloud\EnhancedEcommerce\Plugin\Checkout\Model;

use Magento\Checkout\Model\PaymentInformationManagement as DefaultPaymentInformationManagement;
use MageCloud\EnhancedEcommerce\Model\EventManager;
use MageCloud\EnhancedEcommerce\Model\EventManagerFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use MageCloud\EnhancedEcommerce\Model\EventResolver\BeginCheckout;
use MageCloud\EnhancedEcommerce\Model\EventResolver\AddPaymentInfo;

/**
 * Class PaymentInformationManagement
 * @package MageCloud\EnhancedEcommerce\Plugin\Checkout\Model
 */
class PaymentInformationManagement
{
    /**
     * @var EventManagerFactory
     */
    private $eventManagerFactory;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @param EventManagerFactory $eventManagerFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        EventManagerFactory $eventManagerFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->eventManagerFactory = $eventManagerFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param DefaultPaymentInformationManagement $subject
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return null
     * @throws NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        DefaultPaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        /** @var EventManager $eventManager */
        $eventManager = $this->eventManagerFactory->create(
            [
                'eventArguments' => [
                    'event_type' => BeginCheckout::EVENT_TYPE,
                    'cart_id' => $cartId
                ]
            ]
        );
        $this->checkoutSession->setBeginCheckoutDataLayerEventData(null);
        $this->checkoutSession->setBeginCheckoutDataLayerEventData(
            $eventManager->setEvenstInitializedCount()
                ->initEvent()
        );
        return null;
    }

    /**
     * @param DefaultPaymentInformationManagement $subject
     * @param $result
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterSavePaymentInformation(
        DefaultPaymentInformationManagement $subject,
        $result,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        /** @var EventManager $eventManager */
        $eventManager = $this->eventManagerFactory->create(
            [
                'eventArguments' => [
                    'event_type' => AddPaymentInfo::EVENT_TYPE,
                    'cart_id' => $cartId
                ]
            ]
        );
        $this->checkoutSession->setAddPaymentInfoDataLayerEventData(null);
        $this->checkoutSession->setAddPaymentInfoDataLayerEventData(
            $eventManager->setEvenstInitializedCount()
                ->initEvent()
        );
        return $result;
    }
}