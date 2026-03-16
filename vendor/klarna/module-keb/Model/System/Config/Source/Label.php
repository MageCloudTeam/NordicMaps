<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Returning the values for the Keb themes
 *
 * @internal
 */
class Label implements OptionSourceInterface
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
                'value' => 'default',
                'label' => __('Default'),
            ],
            [
                'value' => 'klarna',
                'label' => __('Klarna'),
            ]
        ];
    }
}
