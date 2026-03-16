<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes\Addresses;

use Klarna\Kp\Model\Api\Request\Builder as RequestBuilder;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Builder
{
    /**
     * @var Mapper
     */
    private Mapper $mapper;

    /**
     * @param Mapper $mapper
     * @codeCoverageIgnore
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Adding the addresses to the request builder
     *
     * @param RequestBuilder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(RequestBuilder $requestBuilder, CartInterface $magentoQuote): void
    {
        $billingAddressMapping = $this->mapper->getKlarnaDataFromAddress(
            $magentoQuote->getStore(),
            $magentoQuote->getBillingAddress()
        );
        $requestBuilder->setBillingAddress($billingAddressMapping);

        if (!$magentoQuote->getIsVirtual()) {
            $shippingAddressMapping = $this->mapper->getKlarnaDataFromAddress(
                $magentoQuote->getStore(),
                $magentoQuote->getShippingAddress()
            );
            $requestBuilder->setShippingAddress($shippingAddressMapping);
        }
    }
}
