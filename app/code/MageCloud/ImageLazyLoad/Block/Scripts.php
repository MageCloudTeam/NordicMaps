<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ImageLazyLoad\Block;

use MageCloud\ImageLazyLoad\Helper\Config;
use Magento\Framework\View\Element\Template;

/**
 * Class Scripts
 */
class Scripts extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Scripts constructor.
     *
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if($this->getConfig()->hasModuleEnabled() === false) {
            return '';
        }

        return parent::_toHtml();
    }
}