<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kss\Api;

/**
 * Interface for the shipping method gateway
 *
 * @api
 */
interface ShippingMethodGatewayInterface
{
    /**
     * Setting the session id to which the shipping gateway information are associated
     *
     * @param string $checkoutId
     * @return $this
     */
    public function setKlarnaSessionId($checkoutId): ShippingMethodGatewayInterface;

    /**
     * Getting back the session id to which the shipping gateway information are associated
     *
     * @return string
     */
    public function getKlarnaSessionId(): string;

    /**
     * Setting the shipping amount
     *
     * @param float $amount
     * @return ShippingMethodGatewayInterface
     */
    public function setShippingAmount($amount): ShippingMethodGatewayInterface;

    /**
     * Getting back the shipping amount
     *
     * @return float
     */
    public function getShippingAmount(): float;

    /**
     * Setting the tax amount
     *
     * @param float $tax
     * @return ShippingMethodGatewayInterface
     */
    public function setTaxAmount($tax): ShippingMethodGatewayInterface;

    /**
     * Getting back the tax amount
     *
     * @return float
     */
    public function getTaxAmount(): float;

    /**
     * Setting the tax rate
     *
     * @param float $rate
     * @return ShippingMethodGatewayInterface
     */
    public function setTaxRate($rate): ShippingMethodGatewayInterface;

    /**
     * Getting back the tax rate
     *
     * @return float
     */
    public function getTaxRate(): float;

    /**
     * Setting the is_active flag
     *
     * @param bool $flag
     * @return ShippingMethodGatewayInterface
     */
    public function setIsActive(bool $flag): ShippingMethodGatewayInterface;

    /**
     * Returns true when the shipping information can be used for the respective Klarna quote.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Setting the shipping method id
     *
     * @param string $id
     * @return ShippingMethodGatewayInterface
     */
    public function setShippingMethodId($id): ShippingMethodGatewayInterface;

    /**
     * Getting back the shipping method id
     *
     * @return string
     */
    public function getShippingMethodId(): string;

    /**
     * Setting the pick up point flag
     *
     * @param bool $flag
     * @return ShippingMethodGatewayInterface
     */
    public function setPickUpPointFlag(bool $flag): ShippingMethodGatewayInterface;

    /**
     * Getting back the pick up point flag
     *
     * @return bool
     */
    public function isPickUpPoint(): bool;

    /**
     * Setting the pick up point name
     *
     * @param string $name
     * @return ShippingMethodGatewayInterface
     */
    public function setPickUpPointName(string $name): ShippingMethodGatewayInterface;

    /**
     * Getting back the pick up point name
     *
     * @return string
     */
    public function getPickUpPointName(): string;

    /**
     * Setting the delivery details
     *
     * @param string $details
     * @return ShippingMethodGatewayInterface
     */
    public function setDeliveryDetails(string $details): ShippingMethodGatewayInterface;

    /**
     * Getting back the delivery details
     *
     * @return string
     */
    public function getDeliveryDetails(): ?string;

    /**
     * Setting the shipping method name
     *
     * @param string $name
     * @return ShippingMethodGatewayInterface
     */
    public function setName(string $name): ShippingMethodGatewayInterface;

    /**
     * Getting back the shipping method name
     *
     * @return string
     */
    public function getName(): string;
}
