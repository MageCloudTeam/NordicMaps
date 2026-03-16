<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Shipping;

use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Calculator\Shipping as ShippingCalculator;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * @internal
 */
class PostPurchaseCalculator extends CalculatorAbstract
{
    /**
     * @var ShippingCalculator
     */
    private ShippingCalculator $calculator;

    /**
     * @param DataConverter $dataConverter
     * @param ShippingCalculator $calculator
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter, ShippingCalculator $calculator)
    {
        parent::__construct($dataConverter);
        $this->calculator = $calculator;
    }

    /**
     * Calculating the values
     *
     * @param DataHolder $dataHolder
     * @param OrderInterface $order
     */
    public function calculate(DataHolder $dataHolder, OrderInterface $order): void
    {
        $this->reset();

        $unitPrice = $dataHolder->getBaseShippingInclTax();
        $reference = $order->getShippingMethod();
        if ($reference === null) {
            $reference = '';
        }

        $unitPriceConverted = $this->dataConverter->toApiFloat($unitPrice);
        $this->reset()
            ->setUnitPrice($unitPriceConverted)
            ->setTaxRate($this->dataConverter->toApiFloat($this->calculator->getTaxRate($dataHolder)))
            ->setTotalAmount($unitPriceConverted)
            ->setTaxAmount($this->dataConverter->toApiFloat($order->getShippingTaxAmount()))
            ->setTitle(__('Shipping & Handling (' . $order->getShippingDescription() . ')')->getText())
            ->setReference($reference);

        $this->discountAmount = 0;
    }
}
