<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Discount;

use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;

/**
 * @internal
 */
class Processor
{
    /**
     * @var PrePurchaseCalculator
     */
    private PrePurchaseCalculator $calculator;
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;

    /**
     * @param PrePurchaseCalculator $calculator
     * @param KlarnaConfig $klarnaConfig
     * @codeCoverageIgnore
     */
    public function __construct(PrePurchaseCalculator $calculator, KlarnaConfig $klarnaConfig)
    {
        $this->calculator = $calculator;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Processing the data and putting the data into the Parameter instance
     *
     * @param DataHolder $dataHolder
     * @param Parameter $parameter
     */
    public function processPrePurchase(DataHolder $dataHolder, Parameter $parameter): void
    {
        if ($this->klarnaConfig->isSeparateTaxLine($dataHolder->getStore())) {
            $this->calculator->calculateSeparateTaxLineData($dataHolder);
        } else {
            $this->calculator->calculateIncludedTaxData($dataHolder);
        }

        $parameter->setDiscountUnitPrice($this->calculator->getUnitPrice());
        $parameter->setDiscountTaxRate($this->calculator->getTaxRate());
        $parameter->setDiscountTotalAmount($this->calculator->getTotalAmount());
        $parameter->setDiscountTaxAmount($this->calculator->getTaxAmount());
        $parameter->setDiscountTitle($this->calculator->getTitle());
        $parameter->setDiscountReference($this->calculator->getReference());
    }
}
