<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\HideDefaultStoreCode\Plugin\Model;

use MageCloud\HideDefaultStoreCode\Model\Config;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddDefaultStoreCode
 */
class AddDefaultStoreCode
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
     * @param StoreManager $subject
     * @param string $store
     *
     * @return array
     */
    public function beforeSetCurrentStore(StoreManager $subject, $store)
    {
        if ($this->config->isHideDefaultStoreCode() === true) {
            if($store === '') {
                $store = $this->storeManager->getDefaultStoreView()->getCode();
            }
        }

        return [$store];
    }
}