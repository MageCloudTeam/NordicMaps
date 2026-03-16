<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Shipping;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\Calculator\Shipping as ShippingCalculator;
use Magento\Tax\Model\Config;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Quote\Model\Quote\Address;

/**
 * @internal
 */
class PrePurchaseCalculator extends CalculatorAbstract
{
    /**
     * @var ShippingCalculator
     */
    private ShippingCalculator $calculator;
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param DataConverter $dataConverter
     * @param ShippingCalculator $calculator
     * @param Config $config
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter, ShippingCalculator $calculator, Config $config)
    {
        parent::__construct($dataConverter);

        $this->calculator = $calculator;
        $this->config = $config;
    }

    /**
     * Calculating data without taxes
     *
     * @param DataHolder $dataHolder
     */
    public function calculateSeparateTaxLineData(DataHolder $dataHolder): void
    {
        $address = $dataHolder->getShippingAddress();
        $discountAmount = $address->getBaseShippingDiscountAmount();

        $totalAmount = $address->getBaseShippingAmount() - $discountAmount;
        $unitPrice = $address->getBaseShippingAmount();

        $this->reset()
            ->setUnitPrice($this->dataConverter->toApiFloat($unitPrice))
            ->setTotalAmount($this->dataConverter->toApiFloat($totalAmount))
            ->setTitle(__('Shipping & Handling (' . $address->getShippingDescription() . ')')->getText())
            ->setReference((string)$address->getShippingMethod());

        $this->discountAmount = $this->dataConverter->toApiFloat($address->getBaseShippingDiscountAmount());
    }

    /**
     * Calculating data with taxes
     *
     * @param DataHolder $dataHolder
     */
    public function calculateIncludedTaxData(DataHolder $dataHolder): void
    {
        $address = $dataHolder->getShippingAddress();

        $prices = $this->getPricesIncludingTax($address, $dataHolder->getStore());

        $this->reset()
            ->setUnitPrice($this->dataConverter->toApiFloat($prices['unit_price']))
            ->setTaxRate($this->dataConverter->toApiFloat($this->calculator->getTaxRate($dataHolder)))
            ->setTotalAmount($this->dataConverter->toApiFloat($prices['total_amount']))
            ->setTaxAmount($this->dataConverter->toApiFloat($address->getBaseShippingTaxAmount()))
            ->setTitle(__('Shipping & Handling (' . $address->getShippingDescription() . ')')->getText())
            ->setReference((string)$address->getShippingMethod());

        $this->discountAmount = $this->dataConverter->toApiFloat($address->getBaseShippingDiscountAmount());
    }

    /**
     * Getting back different prices including tax
     *
     * @param Address $address
     * @param StoreInterface $store
     * @return array
     */
    private function getPricesIncludingTax(Address $address, StoreInterface $store): array
    {
        $totalAmount = $address->getBaseShippingInclTax() - $address->getBaseShippingDiscountAmount();
        $unitPrice = $address->getBaseShippingInclTax();

        $isConfigShippingPriceIncludingTaxSet = $this->config->shippingPriceIncludesTax($store);
        if (!$isConfigShippingPriceIncludingTaxSet) {
            $totalAmount = $address->getBaseShippingAmount() +
                $address->getBaseShippingTaxAmount() -
                $address->getBaseShippingDiscountAmount();
            $unitPrice = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount();
        }

        return [
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount
        ];
    }
}
