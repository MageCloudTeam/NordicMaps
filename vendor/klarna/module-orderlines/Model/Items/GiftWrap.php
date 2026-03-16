<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items;

use Klarna\Orderlines\Api\OrderLineInterface;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Calculator\GiftWrap as GiftWrapCalculator;
use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;

/**
 * Generate order gift wrapping item details
 *
 * @api
 */
class GiftWrap implements OrderLineInterface
{

    /**
     * @var string
     */
    private $itemType;
    /**
     * @var GiftWrapCalculator
     */
    private $giftWrapCalculator;

    /**
     * @param GiftWrapCalculator $giftWrapCalculator
     * @param string             $itemType
     * @codeCoverageIgnore
     */
    public function __construct(
        GiftWrapCalculator $giftWrapCalculator,
        $itemType = 'surcharge'
    ) {
        $this->itemType           = $itemType;
        $this->giftWrapCalculator = $giftWrapCalculator;
    }

    /**
     * @inheritDoc
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        $giftWrapItem = $this->processItems($dataHolder);
        return $this->setGiftWrapItems($dataHolder, $parameter, $giftWrapItem);
    }

    /**
     * Process items
     *
     * @param DataHolder $dataHolder
     * @return array
     * @throws KlarnaException
     */
    private function processItems(DataHolder $dataHolder): array
    {
        $giftWrapItem = [];
        /** @var \Magento\Quote\Api\Data\CartItemInterface $item */
        foreach ($dataHolder->getItems() as $item) {
            if (!$item->getGwId()) {
                continue;
            }

            $itemToProcess = [
                'sku'                       => $item->getSku(),
                'gift_wrap_base_price'      => $item->getGwBasePrice(),
                'gift_wrap_price'           => $item->getGwPrice(),
                'tax_percent'               => $item->getTaxPercent(),
                'gift_wrap_base_tax_amount' => $item->getGwBaseTaxAmount()
            ];

            $giftWrapItem[] = $this->giftWrapCalculator->getProcessedItem(
                $itemToProcess,
                $this->getQuantity($item, $dataHolder),
                $item->getStore(),
                $this->itemType
            );
        }
        return $giftWrapItem;
    }

    /**
     * Getting back the gift wrap quantity
     *
     * @param mixed      $item
     * @param DataHolder $dataHolder
     * @return int
     */
    private function getQuantity($item, DataHolder $dataHolder): int
    {
        if ($item instanceof QuoteItem) {
            return (int) $item->getQty();
        }

        /**
         * We expect if we don't have a quote item that all flat items are having the same type.
         * We're using this type to decide which qty field should be returned.
         */
        $items = $dataHolder->getFlatItems();

        if (is_array($items)) {
            $flatItem = array_shift($items);
            if ($flatItem instanceof CreditmemoItem) {
                return (int) $item->getQtyRefunded();
            }
            return 0;
        }

        $items = $dataHolder->getFlatItems()->getItems();
        $flatItem = array_shift($items);
        if ($flatItem instanceof InvoiceItem) {
            return (int) $item->getQtyInvoiced();
        }

        return 0;
    }

    /**
     * Setting the gift wrap items to the parameter instance
     *
     * @param DataHolder $dataHolder
     * @param Parameter  $parameter
     * @param array      $giftWrapItem
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    private function setGiftWrapItems(DataHolder $dataHolder, Parameter $parameter, array $giftWrapItem)
    {
        $item = $this->giftWrapCalculator->getItem($dataHolder, $this->itemType);
        if ($item) {
            $giftWrapItem[] = $item;
        }

        $parameter->setGiftWrapItems($giftWrapItem);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        $giftWrapItem = $this->processItems($dataHolder);

        $dataHolder->setGiftWrapId($order->getGwId())
            ->setGiftWrapBasePrice($order->getGwBasePrice())
            ->setStore($order->getStore())
            ->setBillingAddress($order->getBillingAddress());

        return $this->setGiftWrapItems($dataHolder, $parameter, $giftWrapItem);
    }

    /**
     * @inheritDoc
     */
    public function fetch(Parameter $parameter)
    {
        if ($parameter->getGiftWrapItems()) {
            foreach ($parameter->getGiftWrapItems() as $item) {
                $parameter->addOrderLine($item);
            }
        }
        return $this;
    }
}
