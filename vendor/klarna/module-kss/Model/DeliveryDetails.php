<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Klarna\Kss\Api\DeliveryDetailsInterface;
use Klarna\Kss\Api\ShippingMethodGatewayRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class DeliveryDetails implements DeliveryDetailsInterface
{
    /**
     * @var ShippingMethodGatewayRepositoryInterface $shippingMethodGatewayRepository
     */
    private $shippingMethodGatewayRepository;

    /**
     * @param ShippingMethodGatewayRepositoryInterface $shippingMethodGatewayRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        ShippingMethodGatewayRepositoryInterface $shippingMethodGatewayRepository
    ) {
        $this->shippingMethodGatewayRepository = $shippingMethodGatewayRepository;
    }

    /**
     * Returns the Klarna shipping delivery data for a given Klarna session id
     *
     * @param string $sessionId
     * @return string|null
     */
    public function getDeliveryDataByKlarnaSessionId(string $sessionId): ?string
    {
        try {
            $shipping = $this->shippingMethodGatewayRepository->loadShipping($sessionId, 'klarna_session_id');
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $shipping->getDeliveryDetails();
    }
}
