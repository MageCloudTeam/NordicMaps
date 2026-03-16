<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Assignment;

use Magento\Framework\DataObject;
use Klarna\Base\Helper\DataConverter;
use Klarna\Kss\Api\ShippingMethodGatewayInterface;

/**
 * @internal
 */
class ShippingMethodGateway
{
    /**
     * @var DataConverter
     */
    private DataConverter $dataConverter;

    /**
     * @param DataConverter $dataConverter
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter)
    {
        $this->dataConverter = $dataConverter;
    }

    /**
     * Adding the shipping information to the KSS model
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @param DataObject                     $klarnaRequest
     * @return ShippingMethodGatewayInterface
     * @codeCoverageIgnore
     */
    public function assignByKlarnaInstance(
        ShippingMethodGatewayInterface $shippingMethodGateway,
        DataObject $klarnaRequest
    ): ShippingMethodGatewayInterface {
        $shippingInformation = $klarnaRequest->getSelectedShippingOption();

        $shippingMethodGateway->setShippingAmount($this->dataConverter->toShopFloat($shippingInformation['price']));
        $shippingMethodGateway->setTaxAmount($this->dataConverter->toShopFloat($shippingInformation['tax_amount']));
        $shippingMethodGateway->setTaxRate($this->dataConverter->toShopFloat($shippingInformation['tax_rate']));
        $shippingMethodGateway->setShippingMethodId($shippingInformation['id']);
        $shippingMethodGateway->setName($shippingInformation['name']);

        $shippingMethodGateway->setPickUpPointFlag(false);
        if ($shippingInformation['shipping_method'] === 'PickUpPoint') {
            $shippingMethodGateway->setPickUpPointFlag(true);
            $shippingMethodGateway->setPickUpPointName($shippingInformation['name']);
        }

        $shippingMethodGateway->setIsActive(true);
        $shippingMethodGateway->setDeliveryDetails(json_encode($shippingInformation['delivery_details']));
        return $shippingMethodGateway;
    }
}
