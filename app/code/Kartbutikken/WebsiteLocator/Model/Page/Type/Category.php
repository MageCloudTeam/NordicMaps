<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model\Page\Type;

use Kartbutikken\WebsiteLocator\Api\UrlRepositoryInterface;
use Kartbutikken\WebsiteLocator\Model\Page\AbstractPage;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

/**
 * Class Category
 */
class Category extends AbstractPage
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Category constructor.
     *
     * @param UrlRepositoryInterface $urlRepository
     * @param UrlInterface $url
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        UrlInterface $url,
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($urlRepository, $request, $data);

        $this->categoryRepository = $categoryRepository;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $oldScope = $this->url->getScope();
        $this->url->setScope($this->getTargetStore()->getId());
        $url = $this->getCategory()->getUrl();
        $this->url->setScope($oldScope);

        return $url;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return !!$this->getCategory()->getIsActive();
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategory(): CategoryInterface
    {
        return $this->categoryRepository->get($this->getEntityId(), $this->getTargetStore()->getId());
    }
}
