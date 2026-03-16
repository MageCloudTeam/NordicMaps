<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
namespace MageCloud\EnhancedEcommerce\Plugin\Checkout\Model;

use Magento\Checkout\Model\ShippingInformationManagement as DefaultShippingInformationManagement;
use Magento\Checkout\Model\Session as CheckoutSession;
use MageCloud\EnhancedEcommerce\Model\EventManager;
use MageCloud\EnhancedEcommerce\Model\EventManagerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use MageCloud\EnhancedEcommerce\Model\EventResolver\AddShippingInfo;

/**
 * Class ShippingInformationManagement
 * @package MageCloud\EnhancedEcommerce\Plugin\Checkout\Model
 */
class ShippingInformationManagement
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
     * @param DefaultShippingInformationManagement $subject
     * @param $result
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return null
     * @throws NoSuchEntityException
     */
    public function afterSaveAddressInformation(
        DefaultShippingInformationManagement $subject,
        $result,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        /** @var EventManager $eventManager */
        $eventManager = $this->eventManagerFactory->create(
            [
                'eventArguments' => [
                    'event_type' => AddShippingInfo::EVENT_TYPE,
                    'cart_id' => $cartId
                ]
            ]
        );
        $this->checkoutSession->setAddShippingInfoDataLayerEventData(null);
        $this->checkoutSession->setAddShippingInfoDataLayerEventData(
            $eventManager->setEvenstInitializedCount()
                ->initEvent()
        );
        return $result;
    }
}