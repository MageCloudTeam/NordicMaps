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
class Service implements OptionSourceInterface
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
                'value' => 'Klarna Checkout',
                'label' => 'Klarna Checkout'
            ],
            [
                'value' => 'Klarna Payments',
                'label' => 'Klarna Payments'
            ],
            [
                'value' => 'Klarna Ordermanagement',
                'label' => 'Klarna Ordermanagement'
            ]
        ];
    }
}
