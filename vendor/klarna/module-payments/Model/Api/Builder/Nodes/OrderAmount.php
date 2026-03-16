<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Base\Helper\DataConverter;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class OrderAmount
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
     * Adding the order amount to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        $address = $magentoQuote->isVirtual()
            ? $magentoQuote->getBillingAddress()
            : $magentoQuote->getShippingAddress();

        $requestBuilder->setOrderAmount((int) $this->dataConverter->toApiFloat($address->getBaseGrandTotal()));
    }
}
