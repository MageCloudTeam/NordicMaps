<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ImageLazyLoad\Model\Controller;

use Closure;
use MageCloud\ImageLazyLoad\Helper\Config;
use MageCloud\ImageLazyLoad\Helper\Filter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class ResultPlugin
 */
class ResultPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $config
     * @param Filter $filter
     */
    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        Filter $filter
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->filter = $filter;
    }

    /**
     * @param LayoutInterface $layout
     * @param $output
     *
     * @return mixed
     */
    public function afterGetOutput(LayoutInterface $layout, $output) {
        if (!$this->config->hasModuleEnabled()) {
            return $output;
        }

        return $this->filter->filter($output);
    }
}
