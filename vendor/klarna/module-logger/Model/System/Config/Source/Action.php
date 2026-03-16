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
class Action implements OptionSourceInterface
{

    /**
     * Getting back the placement IDs
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Cancel order',
                'label' => 'KP: Cancel order'
            ],
            [
                'value' => 'Create Order',
                'label' => 'KP: Create Order'
            ],
            [
                'value' => 'Create session',
                'label' => 'KP: Create session'
            ],
            [
                'value' => 'Read session',
                'label' => 'KP: Read session'
            ],
            [
                'value' => 'Update session',
                'label' => 'KP: Update session'
            ],
            [
                'value' => 'Authorize Callback',
                'label' => 'KP: Authorization callback'
            ],
            [
                'value' => 'Address Update (Callback)',
                'label' => 'KCO: Address Update (Callback)'
            ],
            [
                'value' => 'Create Order',
                'label' => 'KCO: Create Order'
            ],
            [
                'value' => 'Disabled (Callback)',
                'label' => 'KCO: Disabled (Callback)'
            ],
            [
                'value' => 'Get Order',
                'label' => 'KCO: Get Order'
            ],
            [
                'value' => 'Push (Callback)',
                'label' => 'KCO: Push (Callback)'
            ],
            [
                'value' => 'Shipping Method Update (Callback)',
                'label' => 'KCO: Shipping Method Update (Callback)'
            ],
            [
                'value' => 'Update Order',
                'label' => 'KCO: Update Order'
            ],
            [
                'value' => 'Validate (Callback)',
                'label' => 'KCO: Validate (Callback)'
            ],
            [
                'value' => 'Acknowledge order',
                'label' => 'OM: Acknowledge order'
            ],
            [
                'value' => 'Add shipping details to capture',
                'label' => 'OM: Add shipping details to capture'
            ],
            [
                'value' => 'Add shipping info',
                'label' => 'OM: Add shipping info'
            ],
            [
                'value' => 'Cancel order',
                'label' => 'OM: Cancel order'
            ],
            [
                'value' => 'Capture order',
                'label' => 'OM: Capture order'
            ],
            [
                'value' => 'Extend authorization',
                'label' => 'OM: Extend authorization'
            ],
            [
                'value' => 'Get capture',
                'label' => 'OM: Get capture'
            ],
            [
                'value' => 'Get order',
                'label' => 'OM: Get order'
            ],
            [
                'value' => 'Refund',
                'label' => 'OM: Refund'
            ],
            [
                'value' => 'Release authorization',
                'label' => 'OM: Release authorization'
            ],
            [
                'value' => 'Resend order invoice',
                'label' => 'OM: Resend order invoice'
            ],
            [
                'value' => 'Update addresses',
                'label' => 'OM: Update addresses'
            ],
            [
                'value' => 'Update capture billing address',
                'label' => 'OM: Update capture billing address'
            ],
            [
                'value' => 'Update merchant references',
                'label' => 'OM: Update merchant references'
            ],
            [
                'value' => 'Update order items',
                'label' => 'OM: Update order items'
            ],
        ];
    }
}
