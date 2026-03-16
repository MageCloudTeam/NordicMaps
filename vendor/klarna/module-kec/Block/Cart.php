<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * @internal
 */
class Cart extends PlacementAbstract
{
    /**
     * Returns true if the button can be shown on the cart page
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isShowable(): bool
    {
        return $this->isKecEnabled() && $this->isShowablePosition('cart');
    }
}
