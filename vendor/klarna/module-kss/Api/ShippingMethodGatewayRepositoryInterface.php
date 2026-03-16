<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kss\Api;

use Klarna\Kss\Model\ResourceModel\ShippingMethodGateway as ShippingResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface class for the shipping gateway repository
 *
 * @api
 */
interface ShippingMethodGatewayRepositoryInterface
{
    /**
     * Load Klarna shipping
     *
     * @param  string $identifier
     * @param  string $loadField
     * @return ShippingMethodGatewayInterface
     */
    public function loadShipping(string $identifier, string $loadField): ShippingMethodGatewayInterface;

    /**
     * Save Klarna shipping
     *
     * @param  ShippingMethodGatewayInterface $shipping
     * @return ShippingResource
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function save(ShippingMethodGatewayInterface $shipping): ShippingResource;
}
