<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Discount;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Base\Model\Quote\SalesRule;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Validator
{
    /**
     * @var SalesRule
     */
    private SalesRule $salesRule;

    /**
     * @param SalesRule $salesRule
     * @codeCoverageIgnore
     */
    public function __construct(SalesRule $salesRule)
    {
        $this->salesRule = $salesRule;
    }

    /**
     * Returns true if its collectable
     *
     * @param Parameter $parameter
     * @param DataHolder $dataHolder
     * @param CartInterface $quote
     * @return bool
     */
    public function isCollectableForPrePurchase(
        Parameter $parameter,
        DataHolder $dataHolder,
        CartInterface $quote
    ): bool {
        return !$parameter->isShippingLineEnabled() &&
            !$dataHolder->isVirtual() &&
            $this->salesRule->isApplyToShippingUsed($quote) &&
            isset($dataHolder->getTotals()['shipping']);
    }

    /**
     * Returns true if its fetchable
     *
     * @param Parameter $parameter
     * @return bool
     */
    public function isFetchable(Parameter $parameter): bool
    {
        return !$parameter->isVirtual() &&
            !$parameter->isShippingLineEnabled() &&
            $parameter->getDiscountTotalAmount() > 0;
    }
}
