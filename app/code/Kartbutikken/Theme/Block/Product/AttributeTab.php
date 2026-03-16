<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Block\Product;

use Magento\Catalog\Block\Product\View\Description;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class OverviewMap
 */
class AttributeTab extends Description
{
    /**
     * @var string
     */
    private $attribute;

    /**
     * AttributeTab constructor.
     *
     * @param string $attribute
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        string $attribute,
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);

        $this->attribute = $attribute;
        $this->initialize();
    }

    /**
     * @return $this
     */
    public function initialize(): self
    {
        $product = $this->getProduct();

        if($product) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $product->getResource()->getAttribute($this->attribute);
            $this->setData('title', $attribute->getStoreLabel());
        }

        return $this;
    }
}
