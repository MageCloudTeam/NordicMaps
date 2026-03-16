<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Api;

use Magento\Store\Api\Data\StoreInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

interface UrlRepositoryInterface
{
    /**
     * Find rewrite by store id and url
     *
     * @param string $entityType
     * @param string|null $url
     *
     * @return UrlRewrite|null
     */
    public function findUrlRewrite(string $entityType = null, string $url = null): ?UrlRewrite;
}
