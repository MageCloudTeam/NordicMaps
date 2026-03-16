<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

namespace MageCloud\HideDefaultStoreCode\Plugin\Model;

use MageCloud\HideDefaultStoreCode\Model\Config;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class HideDefaultStoreCode
{
    /**
     * Configuration
     *
     * @var Config
     */
    private $config;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * HideDefaultStoreCode constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Plugin replace url
     *
     * @param Store $subject
     * @param string $url
     *
     * @return string
     */
    public function afterGetBaseUrl(Store $subject, $url)
    {
        if ($this->config->isHideDefaultStoreCode() === true) {
            $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();
            $url = str_replace('/' . $defaultStoreCode . '/', '/', $url);
        }

        return $url;
    }
}