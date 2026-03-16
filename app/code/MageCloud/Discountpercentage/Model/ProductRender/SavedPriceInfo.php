<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Discountpercentage\Model\ProductRender;

use MageCloud\Discountpercentage\Api\Data\ProductRender\SavedPriceInfoInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class SavedPriceInfo
 */
class SavedPriceInfo extends AbstractExtensibleModel implements SavedPriceInfoInterface
{
    /**
     * @return boolean
     */
    public function isSavedPrice(): bool
    {
        return !!$this->getData('has_saved_price');
    }

    /**
     * @return string
     */
    public function getSavedPrice(): string
    {
        return (string)$this->getData('saved_price');
    }

    /**
     * @param bool $hasSavedPrice
     *
     * @return SavedPriceInfoInterface
     */
    public function setIsSavedPrice(bool $hasSavedPrice): SavedPriceInfoInterface
    {
        $this->setData('has_saved_price', $hasSavedPrice);

        return $this;
    }

    /**
     * @param string $savedPrice
     *
     * @return SavedPriceInfoInterface
     */
    public function setSavedPrice(string $savedPrice): SavedPriceInfoInterface
    {
        $this->setData('saved_price', $savedPrice);

        return $this;
    }
}