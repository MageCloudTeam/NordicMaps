<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Catalog\Plugin;

use Magento\Catalog\Controller\Product\View;
use Magento\Catalog\Model\ResourceModel\Product;

/**
 * Class AddParamBySku
 */
class AddParamBySku
{
    /**
     * @var Product
     */
    private $product;

    /**
     * AddParamBySku constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @param View $subject
     */
    public function beforeExecute(View $subject) {
        if (($sku = $subject->getRequest()->getParam('sku')) && !$subject->getRequest()->getParam('id')) {
            $params = array_merge($subject->getRequest()->getParams(), ['id' => $this->product->getIdBySku($sku)]);
            $subject->getRequest()->setParams($params);
        }
    }
}
