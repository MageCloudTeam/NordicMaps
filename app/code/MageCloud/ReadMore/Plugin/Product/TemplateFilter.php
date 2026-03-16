<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ReadMore\Plugin\Product;

use Exception;
use Magento\Catalog\Helper\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Result\Page;

/**
 * Class TemplateFilter
 */
class TemplateFilter
{
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * Constructor.
     *
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        FilterProvider $filterProvider
    ) {
        $this->filterProvider = $filterProvider;
    }

    /**
     * Init layout for viewing product page
     *
     * @param View $subject
     * @param View $result
     * @param Page $resultPage
     * @param Product $product
     *
     * @throws Exception
     */
    public function afterInitProductLayout(
        View $subject,
        $result,
        $resultPage,
        $product
    ) {
        $description = $product->getResource()->getAttribute('description')->getFrontend()->getValue($product);
        $filteredDescription = $this->filterProvider->getPageFilter()->filter($description);
        $product->setDescription($filteredDescription);

        return $result;
    }
}
