<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Orderlines\Api;

use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Klarna order line abstract
 *
 * @api
 */
interface OrderLineInterface
{
    /**
     * Collecting the orderline data from the quote
     *
     * @param Parameter     $parameter
     * @param DataHolder    $dataHolder
     * @param CartInterface $quote
     * @return $this
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote);

    /**
     * Fetch
     *
     * @param Parameter $parameter
     * @return $this
     */
    public function fetch(Parameter $parameter);

    /**
     * Collecting the orderline data from the invoice
     *
     * @param Parameter      $parameter
     * @param DataHolder     $dataHolder
     * @param OrderInterface $order
     * @return $this
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order);
}
