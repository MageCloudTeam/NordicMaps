<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Getting and setting values from the shipping gateway table
 *
 * @internal
 */
class ShippingMethodGateway extends AbstractModel implements ShippingMethodGatewayInterface, IdentityInterface
{

    public const CACHE_TAG = 'klarna_shipping_method_gateway';

    /**
     * Get Identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function setKlarnaSessionId($checkoutId): ShippingMethodGatewayInterface
    {
        $this->setData('klarna_session_id', $checkoutId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getKlarnaSessionId(): string
    {
        return $this->_getData('klarna_session_id');
    }

    /**
     * @inheritdoc
     */
    public function setShippingAmount($amount): ShippingMethodGatewayInterface
    {
        $this->setData('shipping_amount', $amount);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShippingAmount(): float
    {
        return (float) $this->_getData('shipping_amount');
    }

    /**
     * @inheritdoc
     */
    public function setTaxAmount($tax): ShippingMethodGatewayInterface
    {
        $this->setData('tax_amount', $tax);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTaxAmount(): float
    {
        return (float) $this->_getData('tax_amount');
    }

    /**
     * @inheritdoc
     */
    public function setTaxRate($rate): ShippingMethodGatewayInterface
    {
        $this->setData('tax_rate', $rate);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTaxRate(): float
    {
        return (float) $this->_getData('tax_rate');
    }

    /**
     * @inheritdoc
     */
    public function setIsActive(bool $flag): ShippingMethodGatewayInterface
    {
        $this->setData('is_active', $flag);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isActive(): bool
    {
        return (bool) $this->_getData('is_active');
    }

    /**
     * @inheritdoc
     */
    public function setShippingMethodId($id): ShippingMethodGatewayInterface
    {
        $this->setData('shipping_method_id', $id);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShippingMethodId(): string
    {
        return $this->_getData('shipping_method_id');
    }

    /**
     * @inheritdoc
     */
    public function setPickUpPointFlag(bool $flag): ShippingMethodGatewayInterface
    {
        $this->setData('is_pick_up_point', $flag);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPickUpPoint(): bool
    {
        return (bool) $this->_getData('is_pick_up_point');
    }

    /**
     * @inheritdoc
     */
    public function setPickUpPointName(string $name): ShippingMethodGatewayInterface
    {
        $this->setData('pick_up_point_name', $name);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPickUpPointName(): string
    {
        return $this->_getData('pick_up_point_name');
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryDetails(string $details): ShippingMethodGatewayInterface
    {
        $this->setData('delivery_details', $details);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryDetails(): ?string
    {
        return $this->_getData('delivery_details');
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): ShippingMethodGatewayInterface
    {
        $this->setData('name', $name);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->_getData('name');
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ShippingMethodGateway::class);
    }
}
