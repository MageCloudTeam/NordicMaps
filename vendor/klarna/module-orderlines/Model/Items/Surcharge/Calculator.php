<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Surcharge;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Items\CalculatorAbstract;

/**
 * @internal
 */
class Calculator extends CalculatorAbstract
{

    /**
     * Calculating the values
     *
     * @param DataHolder $dataHolder
     */
    public function calculate(DataHolder $dataHolder): void
    {
        $this->reset();
        $fpt = $dataHolder->getFptTax();
        $fptTax = $this->dataConverter->toApiFloat($fpt['tax']);

        $this->reset()
            ->setUnitPrice($fptTax)
            ->setTotalAmount($fptTax)
            ->setTitle($fpt['name'])
            ->setReference($fpt['reference']);
    }
}
