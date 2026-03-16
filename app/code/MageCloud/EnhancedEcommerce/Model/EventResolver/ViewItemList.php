<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
declare(strict_types=1);

namespace MageCloud\EnhancedEcommerce\Model\EventResolver;

use MageCloud\EnhancedEcommerce\Model\EventResolverInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Catalog\Model\Product\ProductList\Toolbar;

/**
 * Class ViewItemList
 * @package MageCloud\EnhancedEcommerce\Model\EventResolver
 */
class ViewItemList extends AbstractEventResolver implements EventResolverInterface
{
    /**
     * Indicates the event for which the enhanced e-commerce tag in GTM will be activated
     */
    const EVENT_TYPE = 'view_item_list';

    /**
     *  Indicates the list where the products were shown
     */
    const ITEM_LIST_CATEGORY = 'Catalog Category';
    const ITEM_LIST_SEARCH_RESULTS = 'Catalog Search Results';

    /**
     * @return Category|null
     */
    private function initCategory()
    {
        return $this->registry->registry('current_category') ?? $this->registry->registry('category');
    }

    /**
     * @param AbstractCollection $productCollection
     * @param $store
     * @return AbstractCollection
     */
    private function prepareCollection(AbstractCollection $productCollection)
    {
        $productCollection->setCurPage($this->helperData->getCatalogListCurrentPage())
            ->setPageSize($this->helperData->getCatalogListCurrentLimit());
        return $productCollection;
    }

    /**
     * @param AbstractCollection $productCollection
     * @param Category|null $currentCategory
     * @param Store|null $store
     * @param array $eventArguments
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    private function initItems(
        AbstractCollection $productCollection,
        Category $currentCategory = null,
        Store $store = null,
        array $eventArguments = []
    ): array {
        $productCollection = $this->prepareCollection($productCollection);

        $items = [];
        $brandAttribute = $this->getItemBrandAttribute($store);
        foreach ($productCollection as $key => $product) {
            $items[$key] = [
                'item_name' => (string)$product->getName(),
                'item_id' => $product->getData($this->helperData->getProductIdentifier($store)),
                'price' => $this->getItemPrice($product),
                'item_list_name' => $eventArguments['item_list_name'] ?? self::ITEM_LIST_CATEGORY,
                'index' => $key,
                'quantity' => 1
            ];
            if ($currentCategory) {
                $items[$key]['item_list_id'] = $currentCategory->getId();
            }
            if ($brandAttribute && ($brandAttributeValue = $product->getAttributeText($brandAttribute))) {
                $items[$key]['item_brand'] = $brandAttributeValue;
            }
            $items[$key] = array_merge($items[$key], $this->buildCategoriesData($product, $store));
        }

        return array_values($items);
    }

    /**
     * @param Store|null $store
     * @param array $eventArguments
     * @return void
     * @throws LocalizedException
     */
    protected function initEventData(Store $store = null, array $eventArguments = []): void
    {
        $category = $this->initCategory();
        $productCollection = $eventArguments['collection'] ?? null;
        if (!$productCollection instanceof AbstractCollection) {
            return;
        }

        $this->_data = [
            self::DATA_LAYER_EVENT_KEY => $eventArguments['event_type'] ?? self::EVENT_TYPE,
            self::DATA_LAYER_ECOMMERCE_KEY => []
        ];
        $items = $this->initItems($productCollection, $category, $store, $eventArguments);
        if (!empty($items)) {
            $this->_data[self::DATA_LAYER_ECOMMERCE_KEY]['items'] = $items;
        }
    }

    /**
     * @inheirtDoc
     */
    public function resolve(array $eventArguments = []): string
    {
        $eventType = $eventArguments['event_type'] ?? '';
        $store = $eventArguments['store'] ?? null;
        if (!$this->isEnabled($store)) {
            return '';
        }
        // that in case if there are any error during data collect don't break a current processing
        try {
            $this->initEventData($store, $eventArguments);
        } catch (\Exception $e) {
            // omit exception
        }
        return $this->renderEventData($eventType);
    }
}