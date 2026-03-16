<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\ViewModel;

use Kartbutikken\Theme\Model\Config\Web\WebsiteSwitcher;
use Kartbutikken\WebsiteLocator\Model\PageInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
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
     * @var PageInterface
     */
    private $page;

    /**
     * SwitcherUrlProvider constructor.
     *
     * @param EncoderInterface $encoder
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param WebsiteSwitcher $websiteSwitcher
     * @param PageInterface $page
     */
    public function __construct(
        EncoderInterface $encoder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        WebsiteSwitcher $websiteSwitcher,
        PageInterface $page
    ) {
        parent::__construct($encoder, $storeManager, $urlBuilder);

        $this->storeManager = $storeManager;
        $this->websiteSwitcher = $websiteSwitcher;
        $this->page = $page;
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

    /**
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     *
     * @return string|null
     */
    public function getUrlOfAnotherStoreId(StoreInterface $fromStore, StoreInterface $targetStore): ?string
    {
        return $this->page->getUrlOfAnotherStoreId($fromStore, $targetStore);
    }
}
