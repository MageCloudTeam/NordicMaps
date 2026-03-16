<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model\Page;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Interface TypeInterface
 */
interface TypeInterface
{
    /**
     * Return url
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
