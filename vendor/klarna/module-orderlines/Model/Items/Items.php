<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Api\OrderLineInterface;
use Klarna\Orderlines\Model\Calculator\Item as ItemCalculator;
use Klarna\Orderlines\Model\Calculator\ItemShippingAttributes;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Generate order item line details
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 */
class Items implements OrderLineInterface
{

    /**
     * Checkout item types
     */
    public const ITEM_TYPE_PHYSICAL = 'physical';
    public const ITEM_TYPE_VIRTUAL  = 'digital';

    /** @var ItemCalculator $calculator */
    private $calculator;
    /** @var ItemShippingAttributes $attributes */
    private $attributes;
    /**
     * @var bool
     */
    private $collectShippingAttributes = false;
    /**
     * @var Collection
     */
    private $productCollection;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ItemResolverInterface|null
     */
    private ?ItemResolverInterface $itemResolver;

    /**
     * @param ItemCalculator $calculator
     * @param ItemShippingAttributes $attributes
     * @param CollectionFactory $collectionFactory
     * @param ItemResolverInterface|null $itemResolver
     * @codeCoverageIgnore
     */
    public function __construct(
        ItemCalculator $calculator,
        ItemShippingAttributes $attributes,
        CollectionFactory $collectionFactory,
        ItemResolverInterface $itemResolver = null
    ) {
        $this->calculator = $calculator;
        $this->attributes = $attributes;
        $this->collectionFactory = $collectionFactory;
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(
            ItemResolverInterface::class
        );
    }

    /**
     * @inheritDoc
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        $this->collectShippingAttributes = true;
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * Collecting the orderline data
     *
     * @param Parameter  $parameter
     * @param DataHolder $dataHolder
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    private function collect(Parameter $parameter, DataHolder $dataHolder): self
    {
        $items = [];

        $this->updateProductCollection($dataHolder);
        /** @var OrderItemInterface|CartItemInterface $item */
        foreach ($dataHolder->getItems() as $item) {
            $itemInput = $this->createItemInput($item);

            $parentItemInput = [];
            $product = $this->getItemProduct($item);
            if ($product instanceof ProductInterface) {
                $itemInput['product_url'] = $product->getUrlInStore();
                $itemInput['image_url'] = $this->getImageUrl($product);

                $parentItem = $item->getParentItem();
                if ($this->parentIsAValidOrderItem($parentItem)) {
                    $parentItemInput = [
                        'product'      => $parentItem->getProduct(),
                        'product_type' => $parentItem->getProductType(),
                        'qty'          => $parentItem->getQty()
                    ];
                    $itemInput['qtyMultiplier'] = $parentItem['qty'];
                }
            }

            $processedItem = $this->getCollectableItem($itemInput, $parentItemInput);

            if ($processedItem !== null) {
                $items[] = $processedItem;
            }
        }

        $parameter->setItems($items);
        return $this;
    }

    /**
     * Check to see if an order item, parent item is a valid product or not
     *
     * @param ExtensibleDataInterface|null $parentItem
     * @return bool
     */
    private function parentIsAValidOrderItem(?ExtensibleDataInterface $parentItem): bool
    {
        return
            (
                $parentItem instanceof CartItemInterface ||
                $parentItem instanceof OrderItemInterface
            ) &&
            $parentItem->getProduct() instanceof ProductInterface;
    }

    /**
     * Creating the item input data
     *
     * @param OrderItemInterface|CartItemInterface $item
     * @return array
     */
    private function createItemInput($item): array
    {
        $qty = $item->getQty();
        if ($qty === null) {
            $qty = $item->getQtyOrdered();
        }

        if ($item->getCurrentInvoiceRefundItemQty()) {
            $qty = $item->getCurrentInvoiceRefundItemQty();
        }

        return [
            'base_original_price'                   => $item->getBaseOriginalPrice(),
            'base_price'                            => $item->getBasePrice(),
            'base_price_incl_tax'                   => $item->getBasePriceInclTax(),
            'base_row_total'                        => $item->getCurrentInvoiceRefundItemBaseRowTotal()
                ?: $item->getBaseRowTotal(),
            'base_row_total_incl_tax'               => $item->getCurrentInvoiceRefundItemBaseRowTotalInclTax()
                ?: $item->getBaseRowTotalInclTax(),
            'base_tax_amount'                       => $item->getCurrentInvoiceRefundItemBaseTaxAmount()
                ?: $item->getBaseTaxAmount(),
            'base_discount_amount'                  => $item->getCurrentInvoiceRefundItemBaseDiscountAmount()
                ?: $item->getBaseDiscountAmount(),
            'name'                                  => $item->getName(),
            'qty'                                   => $qty,
            'sku'                                   => $item->getSku(),
            'store'                                 => $item->getStore(),
            'tax_percent'                           => $item->getTaxPercent(),
            'product'                               => $item->getProduct(),
            'product_type'                          => $item->getProductType(),
            'qtyMultiplier'                         => 1
        ];
    }

    /**
     * Updating the product collection
     *
     * @param DataHolder $dataHolder
     */
    private function updateProductCollection(DataHolder $dataHolder)
    {
        $ids = [];
        foreach ($dataHolder->getItems() as $item) {
            $ids[] = $item->getProductId();
        }
        $store = $dataHolder->getStore();

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect($this->attributes->getMappedShippingAttribute($store, 'length'));
        $collection->addAttributeToSelect($this->attributes->getMappedShippingAttribute($store, 'width'));
        $collection->addAttributeToSelect($this->attributes->getMappedShippingAttribute($store, 'height'));
        $collection->addAttributeToSelect('weight');
        $collection->addFieldToFilter('entity_id', ['in' => $ids]);

        $this->productCollection = $collection;
    }

    /**
     * @inheritDoc
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * Getting back a prepared and collectable item
     *
     * @param array $item
     * @param array $parentItem
     * @return array|null
     * @throws \Klarna\Base\Exception
     */
    private function getCollectableItem(array $item, array $parentItem)
    {
        if ($this->calculator->isItemUnprocessable($parentItem, $item)) {
            return null;
        }

        $result = $this->calculator->getProcessedItem($item['store'], $item);
        if ($this->collectShippingAttributes) {
            $sku = $item['sku'];

            if ($this->isItemCustomizable($item)) {
                // We want to use the main product SKU as the selected custom option
                // might have it's own SKU which doesn't map to an actual product
                $sku = $item['product']->getData('sku');
            }

            $products = $this->productCollection->getItemsByColumnValue('sku', $sku);
            if (empty($products)) {
                return null;
            }

            $shippingAttributesProduct = array_shift($products);
            $result = $this->attributes->addShippingAttributes($result, $shippingAttributesProduct);
        }

        return $result;
    }

    /**
     * Get image for product
     *
     * @param ProductInterface $product
     * @return string
     */
    private function getImageUrl(ProductInterface $product)
    {
        if (!$product->getSmallImage()) {
            return null;
        }
        $baseUrl = $product->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $baseUrl . 'catalog/product' . $product->getSmallImage();
    }

    /**
     * Return the product from an item
     *
     * @param ItemInterface|Interceptor $item
     * @return mixed
     */
    private function getItemProduct($item)
    {
        if ($item instanceof ItemInterface) {
            return $this->itemResolver->getFinalProduct($item);
        }
        return $item->getProduct();
    }

    /**
     * @inheritDoc
     */
    public function fetch(Parameter $parameter)
    {
        if ($parameter->getItems()) {
            foreach ($parameter->getItems() as $item) {
                $parameter->addOrderLine($item);
            }
        }

        return $this;
    }

    /**
     * Returns true for items that have custom options
     *
     * @param array $item
     * @return bool
     */
    private function isItemCustomizable(array $item): bool
    {
        return isset($item['product']->getCustomOptions()['option_ids']) ||
            $item['product']->getTypeId() === BundleType::TYPE_CODE;
    }
}
