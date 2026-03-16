<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Returning the values for the api mode
 *
 * @internal
 */
class Mode implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return[
            [
                'value' => '1',
                'label' => __('Playground')
            ],
            [
                'value' => '0',
                'label' => __('Production')
            ]
        ];
    }
}
