<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\WeltPixelOwlCarouselSlider\ViewModel;

use MageCloud\WeltPixelOwlCarouselSlider\Model\Owl\ConfigurationConverter;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class OwlConfig
 */
class OwlConfig implements ArgumentInterface
{
    /**
     * @var ConfigurationConverter
     */
    private $configurationConverter;

    /**
     * OwlConfig constructor.
     *
     * @param ConfigurationConverter $configurationConverter
     */
    public function __construct(ConfigurationConverter $configurationConverter) {
        $this->configurationConverter = $configurationConverter;
    }

    /**
     * @param array $config
     * @param array $breakPoints
     *
     * @return array
     */
    public function getConfig(array $config, array $breakPoints): array
    {
        return $this->configurationConverter->convert($config, $breakPoints);
    }
}
