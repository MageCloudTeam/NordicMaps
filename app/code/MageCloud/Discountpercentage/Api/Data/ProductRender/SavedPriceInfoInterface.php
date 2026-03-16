<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Discountpercentage\Api\Data\ProductRender;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Class SavedPriceInfoInterface
 */
interface SavedPriceInfoInterface extends ExtensibleDataInterface
{
    /**
     * @return boolean
     */
    public function isSavedPrice(): bool;

    /**
     * @return string
     */
    public function getSavedPrice(): string;

    /**
     * @param bool $hasSavedPrice
     *
     * @return \MageCloud\Discountpercentage\Api\Data\ProductRender\SavedPriceInfoInterface
     */
    public function setIsSavedPrice(bool $hasSavedPrice): SavedPriceInfoInterface;

    /**
     * @param string $savedPrice
     *
     * @return \MageCloud\Discountpercentage\Api\Data\ProductRender\SavedPriceInfoInterface
     */
    public function setSavedPrice(string $savedPrice): SavedPriceInfoInterface;
}