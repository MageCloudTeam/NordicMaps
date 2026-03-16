<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ReadMore\Plugin\Catalog;

use Magento\Catalog\Model\Template\Filter;
use Magento\Cms\Model\Template\FilterProvider;

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
     * Filter constructor.
     *
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        FilterProvider $filterProvider
    ) {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @param TemplateFilter $subject
     * @param string $returnValue
     *
     * @return string
     * @throws \Exception
     */
    public function afterFilter(Filter $subject, string $returnValue)
    {
        return $this->filterProvider
            ->getBlockFilter()
            ->filter($returnValue);
    }
}