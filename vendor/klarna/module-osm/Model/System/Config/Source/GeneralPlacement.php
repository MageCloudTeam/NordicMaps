<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Onsitemessaging\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class GeneralPlacement implements OptionSourceInterface
{
    /**
     * Getting back the product placement IDs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'credit-promotion-standard',    'label' => __('Credit Promotion - Standard')],
            ['value' => 'credit-promotion-small',       'label' => __('Credit Promotion - Small')],
            ['value' => 'credit-promotion-badge',       'label' => __('Credit Promotion - Badge')],
            ['value' => 'credit-promotion-auto-size',   'label' => __('Credit Promotion - Auto-Size')],
            ['value' => 'sidebar-promotion-auto-size',  'label' => __('Sidebar Promotion - Auto-Size')],
            ['value' => 'other',                        'label' => __('Other')]
        ];
    }
}
