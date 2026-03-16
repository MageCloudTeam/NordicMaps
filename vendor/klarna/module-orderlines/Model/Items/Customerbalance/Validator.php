<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Customerbalance;

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
        return $dataHolder->getUsedCustomerBalanceAmount() > 0;
    }

    /**
     * Returns true if its fetchable
     *
     * @param Parameter $parameter
     * @return bool
     */
    public function isFetchable(Parameter $parameter): bool
    {
        return $parameter->getCustomerbalanceTotalAmount() > 0;
    }
}
