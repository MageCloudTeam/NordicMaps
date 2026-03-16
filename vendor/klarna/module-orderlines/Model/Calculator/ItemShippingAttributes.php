<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Calculator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Calculating shipping attributes for the order creation and update
 *
 * @api
 */
class ItemShippingAttributes
{

    public const UNIT_INCH = 25.4;
    public const UNIT_CM = 10;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Adding shipping attributes to the given item array
     *
     * @param array            $itemResult
     * @param ProductInterface $product
     * @return array
     */
    public function addShippingAttributes(array $itemResult, ProductInterface $product): array
    {
        $itemResult['shipping_attributes'] = [
            'weight'     => $this->getWeight($product),
            'dimensions' => $this->getDimensions($product),
            'tags'       => $this->getCategories($product)
        ];

        return $itemResult;
    }

    /**
     * Getting back the weight of the product
     *
     * @param ProductInterface $product
     * @return float
     */
    private function getWeight(ProductInterface $product): float
    {
        $unit = $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE,
            $product->getStore()
        );

        $weightCalculator = $this->getWeightCalculator($unit);
        return round($product->getWeight() * $weightCalculator);
    }

    /**
     * Getting back the weight calculator
     *
     * @param string $unit
     * @return float
     */
    private function getWeightCalculator($unit): float
    {
        $weightCalculator = 1000;
        if ($unit === 'lbs') {
            $weightCalculator /= 2.2046;
        }

        return $weightCalculator;
    }

    /**
     * Getting back the product dimensions
     *
     * @param ProductInterface $product
     * @return array
     */
    private function getDimensions(ProductInterface $product): array
    {
        $unit = $this->getMappedShippingAttribute($product->getStore(), 'product_unit');
        $dimensionCalculator = $this->getDimensionCalculator($unit);

        return [
            'height' => $this->getProductStat($product, $dimensionCalculator, 'height'),
            'width'  => $this->getProductStat($product, $dimensionCalculator, 'width'),
            'length' => $this->getProductStat($product, $dimensionCalculator, 'length'),
        ];
    }

    /**
     * Getting back the dimension calculator
     *
     * @param string $unit
     * @return float
     */
    private function getDimensionCalculator(string $unit): float
    {
        switch ($unit) {
            case 'cm':
                return self::UNIT_CM;
            case 'inch':
                return self::UNIT_INCH;
            case 'mm':
            default:
                return 1;
        }
    }

    /**
     * Getting back the categories of the product
     *
     * @param ProductInterface $product
     * @return array
     */
    private function getCategories(ProductInterface $product): array
    {
        $categories = [];

        /** @var Collection $collection */
        $collection = $product->getCategoryCollection();
        if ($collection === null) {
            return $categories;
        }
        $collection->addNameToResult();

        /** @var Category $category */
        foreach ($collection->getItems() as $category) {
            $categories[] = $category->getName();
        }

        return $categories;
    }

    /**
     * Get value of product data (width/height/length)
     *
     * @param ProductInterface $product
     * @param float            $dimensionCalculator
     * @param string           $stat
     * @return float
     */
    private function getProductStat(ProductInterface $product, float $dimensionCalculator, string $stat): float
    {
        $attributeCode = $this->getMappedShippingAttribute($product->getStore(), $stat);
        if (!is_numeric($product->getData($attributeCode))) {
            return 0;
        }
        return round($product->getData($attributeCode) * $dimensionCalculator);
    }

    /**
     * Get shipping attribute codes
     *
     * @param StoreInterface $store
     * @param string         $dimensionCode
     * @return string
     */
    public function getMappedShippingAttribute(StoreInterface $store, string $dimensionCode): string
    {
        return $this->scopeConfig->getValue(
            sprintf('klarna/shipping/%s', $dimensionCode),
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }
}
