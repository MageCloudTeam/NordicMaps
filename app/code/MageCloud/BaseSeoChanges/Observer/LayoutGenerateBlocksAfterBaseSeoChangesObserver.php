<?php
namespace MageCloud\BaseSeoChanges\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config as PageConfig;
use MageCloud\BaseSeoChanges\Helper\Data as HelperData;
use Magento\Framework\Registry;

/**
 * Class LayoutGenerateBlocksAfterBaseSeoChangesObserver
 * @package MageCloud\BaseSeoChanges\Observer
 */
class LayoutGenerateBlocksAfterBaseSeoChangesObserver implements ObserverInterface
{
    /**#@+
     * Robots strategy constants
     */
    const ROBOTS_STRATEGY_NOINDEX_NOFOLLOW = 'NOINDEX,NOFOLLOW';
    const ROBOTS_STRATEGY_NOINDEX_FOLLOW = 'NOINDEX,FOLLOW';
    const ROBOTS_STRATEGY_INDEX_FOLLOW = 'INDEX,FOLLOW';
    /**#@-*/

    /**
     * Marketing and tracking parameters that should trigger NOINDEX,NOFOLLOW
     * Exact match parameters
     */
    const MARKETING_PARAMETERS = [
        'gclid',           // Google Click ID
        'gad_source',      // Google Ads Source
        'srsltid',         // Google Search Result ID
        'fbclid',          // Facebook Click ID
        'msclkid',         // Microsoft Click ID
        'cx',              // Google Custom Search
        'ie',              // Internet Explorer / Encoding parameter
        'cof',             // Custom search parameter
        'siteurl',         // Site URL parameter
        'zanpid',          // Zanox Affiliate Network
        'origin',          // Origin tracking
        '_ga',             // Google Analytics
        'ref',             // Referral tracking
        'referrer',
        'click_id',
        'clickid',
        'affiliate_id',
        'aid',
        'campaign_id',
        'source'
    ];

    /**
     * Marketing parameter patterns (regex) that should trigger NOINDEX,NOFOLLOW
     * These are checked using pattern matching
     */
    const MARKETING_PARAMETER_PATTERNS = [
        '/^utm_/',         // All UTM parameters (utm_source, utm_medium, etc.)
        '/^mc_/',          // All Mailchimp parameters (mc_cid, mc_eid, etc.)
        '/^_bta_/',        // All BTA tracking parameters
    ];

    /**
     * @var Request
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * LayoutGenerateBlocksAfterBaseSeoChangesObserver constructor.
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param PageConfig $pageConfig
     * @param HelperData $helperData
     * @param Registry $registry
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        PageConfig $pageConfig,
        HelperData $helperData,
        Registry $registry
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->pageConfig = $pageConfig;
        $this->helperData = $helperData;
        $this->registry = $registry;
    }

    /**
     * Observer execute function.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $query = $this->request->getQueryValue();
        $pathInfo = $this->request->getOriginalPathInfo();

        $this->processRobotsStrategy($query, $pathInfo);
        $this->processCanonical();

        return $this;
    }

    /**
     * Process robots strategy for specific types of URLs.
     *
     * @param array $query
     * @param string $pathInfo
     * @return $this
     */
    private function processRobotsStrategy($query, $pathInfo)
    {
        if ($this->request->getFullActionName() === 'cms_index_defaultNoRoute') {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            return $this;
        }
        if ($this->request->getFullActionName() === 'cms_noroute_index') {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            return $this;
        }

        if ($this->hasMarketingParameters($query)) {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            return $this;
        }

        // NOINDEX,NOFOLLOW for any page with query parameters (except pagination 'p')
        $filterParams = array_diff_key($query, ['p' => 1]);
        if (!empty($filterParams)) {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            return $this;
        }

        if (preg_match('/^\/page_cache/', $pathInfo)) {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            return $this;
        }

        if (
            preg_match('/^\/catalogsearch/', $pathInfo)
            || preg_match('/^\/catalog\/category/', $pathInfo)
            || preg_match('/^\/catalog\/product/', $pathInfo)
            || preg_match('/^\/customer/', $pathInfo)
            || preg_match('/^\/review\/product\/list/', $pathInfo)
        ) {
            $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_NOINDEX_NOFOLLOW);
            // set noindex, nofollow by default// if query has parameter 'p' and it's only one parameter, then robots must be set to noindex, follow
            if (array_key_exists('p', $query) && (count($query) == 1)) {
                $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_INDEX_FOLLOW);
            } else if (array_key_exists('amp', $query)) {
                // index, follow for AMP pages
                $this->pageConfig->setRobots(self::ROBOTS_STRATEGY_INDEX_FOLLOW);
            }
        }

        return $this;
    }

    /**
     * Process the canonical URL for URLs with query parameters.
     *
     * @return $this
     */
    private function processCanonical()
    {
        $currentUrl = $this->getCurrentUrl();
        $canonicalUrl = $this->getCleanCanonicalUrl($currentUrl);
        $assetCollection = $this->getAssetCollection();
        if (!$assetCollection) {
            return $this;
        }

        $query = $this->request->getQueryValue();

        // If marketing parameters are present, always use clean URL without any parameters
        if ($this->hasMarketingParameters($query)) {
            $canonicalGroup = $assetCollection->getGroupByContentType('canonical');
            if ($canonicalGroup && $canonicalGroup->has($currentUrl)) {
                $canonicalGroup->remove($currentUrl);
            }
            $this->addCanonicalUrl($canonicalUrl);
            return $this;
        }

        if (preg_match('/[?&]p=(\d+)/', $currentUrl, $matches)) {
            $baseUrl = preg_replace('/\?.*/', '', $currentUrl);
            if ($matches[1] == 1) {
                $cleanUrl = $baseUrl;
            } else {
                $cleanUrl = $baseUrl . '?p=' . $matches[1];
            }
            // Remove existing canonical before adding new one
            $canonicalGroup = $assetCollection->getGroupByContentType('canonical');
            if ($canonicalGroup) {
                foreach ($canonicalGroup->getAll() as $identifier => $asset) {
                    $canonicalGroup->remove($identifier);
                }
            }
            $this->addCanonicalUrl($cleanUrl);
            return $this;
        }


        $canonicalGroup = $assetCollection->getGroupByContentType('canonical');
        if (!$canonicalGroup) {
            $this->addCanonicalUrl($canonicalUrl);
        } elseif ($canonicalGroup->has($currentUrl) && ($currentUrl != $canonicalUrl)) {
            $canonicalGroup->remove($currentUrl);
            $this->addCanonicalUrl($canonicalUrl);
        }
        return $this;
    }

    /**
     * Add canonical URL to the page configuration.
     *
     * @param string $canonicalUrl
     * @return $this
     */
    private function addCanonicalUrl($canonicalUrl)
    {
        $this->pageConfig->addRemotePageAsset(
            $canonicalUrl,
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $this;
    }

    /**
     * Retrieve the current page URL.
     *
     * @return string
     */
    private function getCurrentUrl()
    {
        return $this->urlBuilder->getCurrentUrl();
    }

    /**
     * Clean and generate the canonical URL based on the current product or path.
     *
     * @param string $url
     * @return string
     */
    private function getCleanCanonicalUrl($url)
    {
        $currentProduct = $this->registry->registry('current_product');

        $cleanUrl = preg_replace('/\?.*/', '', $url);

        if ($currentProduct) {
            if (strpos($cleanUrl, 'catalog/product/view/id') !== false) {
                $cleanUrl = $this->urlBuilder->getUrl($currentProduct->getUrlKey());
            } else {
                $urlKey = basename(parse_url($cleanUrl, PHP_URL_PATH));
                $cleanUrl = $this->urlBuilder->getBaseUrl() . $urlKey  . '/';
            }
        }

        if ($cleanUrl === $this->urlBuilder->getBaseUrl() || preg_match('#/(home|index\.php)/?$#', $cleanUrl)) {
            $cleanUrl = rtrim($this->urlBuilder->getBaseUrl(), '/');
        }

        return $cleanUrl;
    }

    /**
     * Retrieve the asset collection from the page configuration.
     *
     * @return GroupedCollection|null
     */
    private function getAssetCollection()
    {
        return $this->pageConfig->getAssetCollection();
    }

    /**
     * Check if the query contains any marketing or tracking parameters.
     *
     * @param array $query
     * @return bool
     */
    private function hasMarketingParameters($query)
    {
        if (empty($query) || !is_array($query)) {
            return false;
        }

        // Check exact match parameters
        foreach (self::MARKETING_PARAMETERS as $marketingParam) {
            if (array_key_exists($marketingParam, $query)) {
                return true;
            }
        }

        // Check pattern-based parameters
        foreach (array_keys($query) as $paramName) {
            foreach (self::MARKETING_PARAMETER_PATTERNS as $pattern) {
                if (preg_match($pattern, $paramName)) {
                    return true;
                }
            }
        }

        return false;
    }
}

