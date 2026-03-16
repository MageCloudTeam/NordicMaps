<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Productunit implements OptionSourceInterface
{
    /**
     * Getting back all weight unit labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'mm',
                'value' => 'mm'
            ],
            [
                'label' => 'cm',
                'value' => 'cm'
            ],
            [
                'label' => 'Inch',
                'value' => 'inch'
            ]
        ];
    }
}
