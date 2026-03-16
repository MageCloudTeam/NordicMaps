<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Klarna\Kco\Model\Checkout\Kco\Session;
use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Klarna\Kss\Model\ShippingMethodGatewayFactory;

/**
 * @internal
 */
class Factory
{
    /**
     * @var Session
     */
    private Session $kcoSession;
    /**
     * @var ShippingMethodGatewayFactory
     */
    private ShippingMethodGatewayFactory $factory;

    /**
     * @param Session $kcoSession
     * @param ShippingMethodGatewayFactory $factory
     * @codeCoverageIgnore
     */
    public function __construct(Session $kcoSession, ShippingMethodGatewayFactory $factory)
    {
        $this->kcoSession = $kcoSession;
        $this->factory = $factory;
    }

    /**
     * Creating the instance or returning a given one
     *
     * @param string $klarnaOrderId
     * @return ShippingMethodGatewayInterface
     */
    public function create(string $klarnaOrderId): ShippingMethodGatewayInterface
    {
        $shipping = $this->kcoSession->getKlarnaShippingGateway();
        if ($shipping === null) {
            $shipping = $this->factory->create();
        }
        if (!$shipping->getId()) {
            $shipping->setKlarnaSessionId($klarnaOrderId);
        }

        return $shipping;
    }
}
