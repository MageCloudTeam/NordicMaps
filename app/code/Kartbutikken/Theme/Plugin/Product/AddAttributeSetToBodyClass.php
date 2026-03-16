<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Plugin\Product;

use Magento\Catalog\Helper\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Result\Page;

/**
 * Class AddAttributeSetToBodyClass
 */
class AddAttributeSetToBodyClass
{

    /**
     * Init layout for viewing product page
     *
     * @param View $subject
     * @param View $result
     * @param Page $resultPage
     * @param Product $product
     */
    public function afterInitProductLayout(
        View $subject,
        $result,
        $resultPage,
        $product
    ) {
        $pageConfig = $resultPage->getConfig();
        $pageConfig->addBodyClass('product-attribute-set-' . $product->getAttributeSetId());

        return $result;
    }
}
