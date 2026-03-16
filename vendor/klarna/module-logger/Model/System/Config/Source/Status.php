<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Status implements OptionSourceInterface
{

    /**
     * Getting back the placement IDs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '200',
                'label' => '200'
            ],
            [
                'value' => '201',
                'label' => '201'
            ],
            [
                'value' => '204',
                'label' => '204'
            ],
            [
                'value' => '400',
                'label' => '400'
            ],
            [
                'value' => '403',
                'label' => '403'
            ],
            [
                'value' => '404',
                'label' => '404'
            ],
            [
                'value' => '500',
                'label' => '500'
            ],
            [
                'value' => '503',
                'label' => '503'
            ]
        ];
    }
}
