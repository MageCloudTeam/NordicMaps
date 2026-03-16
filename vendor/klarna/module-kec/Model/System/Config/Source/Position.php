<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Position implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'cart',
                'label' => __('Cart'),
            ],
            [
                'value' => 'product',
                'label' => __('Product'),
            ]
        ];
    }
}
