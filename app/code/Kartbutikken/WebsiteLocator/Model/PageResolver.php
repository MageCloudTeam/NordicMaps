<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model;

use Kartbutikken\WebsiteLocator\Api\UrlRepositoryInterface;
use Kartbutikken\WebsiteLocator\Model\Page\AbstractPage;
use Kartbutikken\WebsiteLocator\Model\Page\EntityList;
use Kartbutikken\WebsiteLocator\Model\Page\Type\HomeFactory;
use Kartbutikken\WebsiteLocator\Model\Page\Type\RouteFactory;

/**
 * Class Page
 */
class PageResolver implements PageResolverInterface
{
    /**
     * @var UrlRepositoryInterface
     */
    private $urlRepository;

    /**
     * @var EntityList
     */
    private $entityList;

    /**
     * @var HomeFactory
     */
    private $homeFactory;

    /**
     * @var RouteFactory
     */
    private $routeFactory;

    /**
     * PageResolver constructor.
     *
     * @param UrlRepositoryInterface $urlRepository
     * @param EntityList $entityList
     * @param HomeFactory $homeFactory
     * @param RouteFactory $routeFactory
     */
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        EntityList $entityList,
        HomeFactory $homeFactory,
        RouteFactory $routeFactory
    ) {
        $this->urlRepository = $urlRepository;
        $this->entityList = $entityList;
        $this->homeFactory = $homeFactory;
        $this->routeFactory = $routeFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(): AbstractPage
    {
        $urlRewrite = $this->urlRepository->findUrlRewrite();

        if($urlRewrite === null) {
            return $this->routeFactory->create();
        }
        try {
            return $this->entityList->getEntityByType(
                $urlRewrite->getEntityType(),
                $urlRewrite->toArray()
            );
        } catch (\Error|\Exception $exception) {
            return $this->homeFactory->create();
        }
    }
}
