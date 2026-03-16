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
use Klarna\Orderlines\Model\Items\CalculatorAbstract;

/**
 * @internal
 */
class Calculator extends CalculatorAbstract
{

    /**
     * Calculating the value
     *
     * @param DataHolder $dataHolder
     */
    public function calculate(DataHolder $dataHolder): void
    {
        $this->reset();

        $amount = $dataHolder->getUsedGiftCardAmount();
        $value = -1 * $this->dataConverter->toApiFloat($amount);
        $total = $dataHolder->getTotals()['giftcardaccount'];

        $this->reset()
            ->setUnitPrice($value)
            ->setTotalAmount($value)
            ->setTitle($total->getTitle()->getText())
            ->setReference($total->getCode());
    }
}
