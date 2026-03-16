<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Discount;

use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Calculator\Shipping;
use Klarna\Orderlines\Model\Items\CalculatorAbstract;

/**
 * @internal
 */
class PrePurchaseCalculator extends CalculatorAbstract
{
    /**
     * @var Shipping
     */
    private Shipping $shippingCalculator;

    /**
     * @param DataConverter $dataConverter
     * @param Shipping $shippingCalculator
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter, Shipping $shippingCalculator)
    {
        parent::__construct($dataConverter);
        $this->shippingCalculator = $shippingCalculator;
    }

    /**
     * Getting back data without taxes
     *
     * @param DataHolder $dataHolder
     */
    public function calculateSeparateTaxLineData(DataHolder $dataHolder): void
    {
        $this->setContainerValues($dataHolder, 0, 0);
    }

    /**
     * Getting back data with taxes
     *
     * @param DataHolder $dataHolder
     */
    public function calculateIncludedTaxData(DataHolder $dataHolder): void
    {
        $address = $dataHolder->getShippingAddress();
        $discountAmount = $address->getBaseShippingDiscountAmount();

        $taxRate = $this->shippingCalculator->getTaxRate($dataHolder);
        $calculated = $discountAmount / (1 + $taxRate / 100);
        $tax_amount = (int) round($discountAmount - $calculated, 3);

        $this->setContainerValues($dataHolder, $taxRate, $tax_amount);
    }

    /**
     * Getting back the result structure
     *
     * @param DataHolder $dataHolder
     * @param float $taxRate
     * @param int $taxAmount
     */
    private function setContainerValues(DataHolder $dataHolder, float $taxRate, int $taxAmount): void
    {
        $address = $dataHolder->getShippingAddress();
        $totals = $dataHolder->getTotals();
        $total = $totals['shipping'];

        $this->reset()
            ->setUnitPrice($this->dataConverter->toApiFloat($address->getBaseShippingDiscountAmount()))
            ->setTaxRate($this->dataConverter->toApiFloat($taxRate))
            ->setTotalAmount($this->dataConverter->toApiFloat($address->getBaseShippingDiscountAmount()))
            ->setTaxAmount($taxAmount)
            ->setTitle((string)$total->getTitle())
            ->setReference((string)$address->getShippingMethod());
    }
}
