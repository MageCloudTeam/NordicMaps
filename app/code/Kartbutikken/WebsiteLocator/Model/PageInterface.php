<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model;

use Magento\Store\Api\Data\StoreInterface;

interface PageInterface
{
    /**
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     *
     * @return string|null
     */
    public function getUrlOfAnotherStoreId(StoreInterface $fromStore, StoreInterface $targetStore): ?string;
}
