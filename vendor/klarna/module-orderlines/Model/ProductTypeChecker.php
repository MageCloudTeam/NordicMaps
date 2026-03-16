<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model;

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @internal
 */
class ProductTypeChecker
{

    /**
     * Returns true if its a bundled product with dynamic price type
     *
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isBundledProductWithDynamicPriceType(ExtensibleDataInterface $item): bool
    {
        return $item->getProductType() === Type::TYPE_BUNDLE &&
            $item->getProduct()->getPriceType() === (string) Price::PRICE_TYPE_DYNAMIC;
    }

    /**
     * Returns true if its a bundled product with fixed price type
     *
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isBundledProductWithFixedPriceType(ExtensibleDataInterface $item): bool
    {
        return $item->getProductType() === Type::TYPE_BUNDLE &&
            $this->isFixedPriceType($item);
    }

    /**
     * Returns true if its a non bundle product
     *
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isNonBundleProduct(ExtensibleDataInterface $item): bool
    {
        return $item->getProductType() != Type::TYPE_BUNDLE;
    }

    /**
     * Returns true if its a product with a fixed price type
     *
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isFixedPriceType(ExtensibleDataInterface $item): bool
    {
        return $item->getProduct()->getPriceType() === (string) Price::PRICE_TYPE_FIXED;
    }
}
