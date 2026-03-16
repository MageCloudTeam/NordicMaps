<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model;

use Hryvinskyi\Base\Helper\VarDumper;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Type
 */
class Page implements PageInterface
{
    /**
     * @var PageResolverInterface
     */
    private $pageResolver;

    /**
     * Page constructor.
     *
     * @param PageResolverInterface $pageResolver
     */
    public function __construct(
        PageResolverInterface $pageResolver
    ) {
        $this->pageResolver = $pageResolver;
    }

    /**
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     *
     * @return string|null
     */
    public function getUrlOfAnotherStoreId(StoreInterface $fromStore, StoreInterface $targetStore): ?string
    {
        $url = $this->pageResolver->get()->setTargetStore($targetStore)->getRealUrl();
        $url = $this->addParamToURL('___store', $targetStore->getCode(), $url);
        $url = $this->addParamToURL('___from_store', $fromStore->getCode(), $url);

        return $url;
    }

    /**
     * @param $key
     * @param $value
     * @param $url
     *
     * @return string
     */
    private function addParamToURL($key, $value, $url): string
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            parse_str($query, $queryParams);
            $queryParams[$key] = $value;
            $url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
        } else {
            $url .= '?' . urlencode($key) . '=' . urlencode($value);
        }

        return $url;
    }
}
