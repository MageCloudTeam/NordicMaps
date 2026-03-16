<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\ViewModel\Store;

use Kartbutikken\Theme\Model\Config\Web\WebsiteSwitcher;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\ViewModel\SwitcherUrlProvider as BaseSwitcherUrlProvider;

/**
 * Class SwitcherUrlProvider
 */
class SwitcherUrlProvider extends BaseSwitcherUrlProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WebsiteSwitcher
     */
    private $websiteSwitcher;

    /**
     * SwitcherUrlProvider constructor.
     *
     * @param EncoderInterface $encoder
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param WebsiteSwitcher $websiteSwitcher
     */
    public function __construct(
        EncoderInterface $encoder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        WebsiteSwitcher $websiteSwitcher
    ) {
        parent::__construct($encoder, $storeManager, $urlBuilder);

        $this->storeManager = $storeManager;
        $this->websiteSwitcher = $websiteSwitcher;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->websiteSwitcher->isEnabled();
    }

    /**
     * Return store id
     *
     * @return StoreManagerInterface
     */
    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * @return array
     */
    public function getWebsites(): array
    {
        $return = [];
        $websites = $this->storeManager->getWebsites();

        if (!$this->websiteSwitcher->getCustomWebsites()) {
            return $websites;
        }

        foreach ($websites as $website) {
            if (in_array($website->getCode(), $this->websiteSwitcher->getCustomWebsites())) {
                $return[] = $website;
            }
        }

        return $return;
    }

    /**
     * @param Website $website
     * @param string $format
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteCountryImage(Website $website, string $format = '.svg')
    {
        $mediaUrl = $this->storeManager->getStore($website->getDefaultStore())
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl . 'flugs' . DIRECTORY_SEPARATOR . $website->getCode() . $format;
    }
}
