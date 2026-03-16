<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model;

use Hryvinskyi\Base\Helper\ArrayHelper;
use Kartbutikken\WebsiteLocator\Api\UrlRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class UrlRepository
 */
class UrlRepository implements UrlRepositoryInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * UrlRepository constructor.
     *
     * @param RequestInterface $request
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        RequestInterface $request,
        UrlFinderInterface $urlFinder
    ) {
        $this->request = $request;
        $this->urlFinder = $urlFinder;
    }

    /**
     * Find rewrite by store id and url
     *
     * @param string|null $entityType
     * @param string|null $url
     *
     * @return UrlRewrite|null
     */
    public function findUrlRewrite(string $entityType = null, string $url = null): ?UrlRewrite
    {
        $params = [
            UrlRewrite::TARGET_PATH => $this->getUrlPath($url)
        ];

        if($entityType !== null) {
            $params[UrlRewrite::ENTITY_TYPE] = $entityType;
        }

        return $this->urlFinder->findOneByData($params);
    }

    /**
     * @param string|null $urlPath
     *
     * @return string
     */
    public function getUrlPath(string $urlPath = null): string
    {
        if ($urlPath === null) {
            $urlPath = $this->request->getPathInfo();
        }

        return ltrim($urlPath, '/');
    }
}
