<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Klarna\Kss\Api\ShippingMethodGatewayRepositoryInterface;
use Klarna\Kss\Model\ResourceModel\ShippingMethodGateway as ShippingResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * The repository for the shipping method gateway
 *
 * @internal
 */
class ShippingMethodGatewayRepository implements ShippingMethodGatewayRepositoryInterface
{
    /**
     * @var ShippingResource $shippingResource
     */
    private $shippingResource;
    /**
     * @var ShippingMethodGatewayFactory $shippingFactory
     */
    private $shippingFactory;

    /**
     * @param ShippingResource             $shippingResource
     * @param ShippingMethodGatewayFactory $shippingFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        ShippingResource             $shippingResource,
        ShippingMethodGatewayFactory $shippingFactory
    ) {
        $this->shippingResource = $shippingResource;
        $this->shippingFactory  = $shippingFactory;
    }

    /**
     * Load Klarna shipping with different methods
     *
     * @param string $identifier
     * @param string $loadField
     * @return ShippingMethodGatewayInterface
     * @throws NoSuchEntityException
     */
    public function loadShipping(string $identifier, string $loadField = 'shipping_id'): ShippingMethodGatewayInterface
    {
        $shipping = $this->shippingFactory->create();
        $this->shippingResource->load($shipping, $identifier, $loadField);
        if (!$shipping->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }

        return $shipping;
    }

    /**
     * @inheritdoc
     */
    public function save(ShippingMethodGatewayInterface $shipping): ShippingResource
    {
        try {
            return $this->shippingResource->save($shipping);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }
}
