<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
namespace MageCloud\EnhancedEcommerce\CustomerData;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class GtmRender
 * @package MageCloud\EnhancedEcommerce\CustomerData
 */
class DataLayerRender extends DataObject implements SectionSourceInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Json $json,
        array $data = []
    ) {
        parent::__construct($data);
        $this->checkoutSession = $checkoutSession;
        $this->json = $json;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $dataLayerEvents = [];
        if (
            $removeFromCartDatalayerEventData = $this->checkoutSession->getRemoveFromCartDataLayerEventData()
        ) {
            $dataLayerEvents[] = $removeFromCartDatalayerEventData;
            $this->checkoutSession->setRemoveFromCartDatalayerEventData(null);
        }
        if ($addToCartDataLayerEventData = $this->checkoutSession->getAddToCartDataLayerEventData()) {
            $dataLayerEvents[] = $addToCartDataLayerEventData;
            $this->checkoutSession->setAddToCartDataLayerEventData(null);
        }
        if (
            $addShippingInfoDataLayerEventData = $this->checkoutSession->getAddShippingInfoDataLayerEventData()
        ) {
            $dataLayerEvents[] = $addShippingInfoDataLayerEventData;
            $this->checkoutSession->setAddShippingInfoDataLayerEventData(null);
        }
        if (
            $addPaymentInfoDataLayerEventData = $this->checkoutSession->getAddPaymentInfoDataLayerEventData()
        ) {
            $dataLayerEvents[] = $addPaymentInfoDataLayerEventData;
            $this->checkoutSession->setAddPaymentInfoDataLayerEventData(null);
        }
        if (
            $beginCheckoutDataLayerEventData = $this->checkoutSession->getBeginCheckoutDataLayerEventData()
        ) {
            $dataLayerEvents[] = $beginCheckoutDataLayerEventData;
            $this->checkoutSession->setBeginCheckoutDataLayerEventData(null);
        }

        return [
            'events' => [array_pop($dataLayerEvents)]
        ];
    }
}