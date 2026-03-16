<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\WeltPixelOwlCarouselSlider\Block\Slider;

use MageCloud\WeltPixelOwlCarouselSlider\ViewModel\OwlConfig;
use MageCloud\WeltPixelOwlCarouselSlider\ViewModel\SwiperConfig;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\MobileDetect\Helper\Data;
use WeltPixel\OwlCarouselSlider\Block\Slider\Custom as BaseCustom;
use WeltPixel\OwlCarouselSlider\Helper\Custom as HelperCustom;

/**
 * Class Custom
 */
class Custom extends BaseCustom
{
    /**
     * @var OwlConfig
     */
    private $owlConfig;

    /**
     * @var SwiperConfig
     */
    private $swiperConfig;

    /**
     * Custom constructor.
     *
     * @param Context $context
     * @param HelperCustom $helperCustom
     * @param FilterProvider $filterProvider
     * @param OwlConfig $owlConfig
     * @param SwiperConfig $swiperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperCustom $helperCustom,
        FilterProvider $filterProvider,
        OwlConfig $owlConfig,
        \Magento\Framework\Registry $registry,
        SwiperConfig $swiperConfig,
        array $data = []
    ) {
        $this->owlConfig = $owlConfig;
        $this->swiperConfig = $swiperConfig;

        parent::__construct(
            $context,
            $registry,
            $helperCustom,
            $filterProvider,
            $data
        );
    }

    /**
     * @return OwlConfig
     */
    public function getOwlConfigViewModel(): OwlConfig
    {
        return $this->owlConfig;
    }

    /**
     * @return SwiperConfig
     */
    public function getSwiperConfigViewModel(): SwiperConfig
    {
        return $this->swiperConfig;
    }
}
