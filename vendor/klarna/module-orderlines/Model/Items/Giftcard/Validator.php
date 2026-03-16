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
class Validator
{
    /**
     * Returns true if its collectable
     *
     * @param DataHolder $dataHolder
     * @return bool
     */
    public function isCollectable(DataHolder $dataHolder): bool
    {
        $totals = $dataHolder->getTotals();

        if (!is_array($totals) || !isset($totals['giftcardaccount'])) {
            return false;
        }
        $total = $totals['giftcardaccount'];
        return $total->getValue() !== 0;
    }

    /**
     * Returns true if its fetchable.
     *
     * Its fetchable when the giftcard amount is not equal to 0 (means it  can be positive or negative).
     *
     * @param Parameter $parameter
     * @return bool
     */
    public function isFetchable(Parameter $parameter): bool
    {
        $value = $parameter->getGiftCardAccountTotalAmount();
        return is_numeric($value) && $value !== 0;
    }
}
