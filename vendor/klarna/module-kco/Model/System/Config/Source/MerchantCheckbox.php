<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Config\Source;

use Klarna\Base\Model\System\Config\Source\Base;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Generate merchant checkbox options
 *
 * @internal
 */
class MerchantCheckbox implements OptionSourceInterface
{
    /**
     * @var Base
     */
    private $base;

    /**
     * @param Base $base
     * @codeCoverageIgnore
     */
    public function __construct(Base $base)
    {
        $this->base = $base;
    }

    /**
     * Get merchant checkbox options and add a disabled option
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $this->base->setOptionName('merchant_checkbox');
        $options = $this->base->toOptionArray();

        return [
            ['value' => -1, 'label' => 'Disabled'],
            ...$options,
        ];
    }
}
