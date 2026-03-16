<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Version implements OptionSourceInterface
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
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->base->setOptionName('api_versions');
        $options = $this->base->toOptionArray();
        foreach ($options as $key => $value) {
            $options[$key]['label'] = __($value['label']);
        }
        sort($options);
        return $options;
    }
}
