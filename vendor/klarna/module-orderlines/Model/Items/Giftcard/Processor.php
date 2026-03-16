<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Giftcard;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;

/**
 * @internal
 */
class Processor
{
    /**
     * @var Calculator
     */
    private Calculator $calculator;

    /**
     * @param Calculator $calculator
     * @codeCoverageIgnore
     */
    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Processing the data and putting the data into the Parameter instance
     *
     * @param DataHolder $dataHolder
     * @param Parameter $parameter
     */
    public function process(DataHolder $dataHolder, Parameter $parameter): void
    {
        $this->calculator->calculate($dataHolder);

        $parameter->setGiftCardAccountUnitPrice($this->calculator->getUnitPrice())
            ->setGiftCardAccountTaxRate($this->calculator->getTaxRate())
            ->setGiftCardAccountTotalAmount($this->calculator->getTotalAmount())
            ->setGiftCardAccountTaxAmount($this->calculator->getTaxAmount())
            ->setGiftCardAccountTitle($this->calculator->getTitle())
            ->setGiftCardAccountReference($this->calculator->getReference());
    }
}
