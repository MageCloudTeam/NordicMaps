<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;

/**
 * Class Related
 */
class Related extends AbstractProduct
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @param Context $context
     * @param Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        Context $context,
        Visibility $catalogProductVisibility,
        array $data = []
    ) {
        $this->catalogProductVisibility = $catalogProductVisibility;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return !!$this->collection->getSize();
    }

    /**
     *
     */
    public function collection()
    {
        $product = $this->getProduct();
        /* @var $product Product */

        $this->collection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();

        $this->collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $this->collection->load();
    }

    /**
     * @return AbstractProduct|void
     */
    protected function _beforeToHtml() {
        $this->collection();
    }
}