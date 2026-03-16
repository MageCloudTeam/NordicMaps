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
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Product
 */
class Product extends AbstractPage
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Status
     */
    private $productStatus;

    /**
     * @var Url
     */
    private $url;

    /**
     * Product constructor.
     *
     * @param UrlRepositoryInterface $urlRepository
     * @param RequestInterface $request
     * @param ProductRepository $productRepository
     * @param Status $productStatus
     * @param Url $url
     * @param array $data
     */
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        RequestInterface $request,
        ProductRepository $productRepository,
        Status $productStatus,
        Url $url,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->productStatus = $productStatus;
        $this->url = $url;

        parent::__construct($urlRepository, $request, $data);
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url->getProductUrl($this->getProduct());
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return in_array($this->getProduct()->getStatus(), $this->productStatus->getVisibleStatusIds());
    }

    /**
     * @param StoreInterface $targetStore
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    private function getProduct(): ProductInterface
    {
        return $this->productRepository->getById($this->getEntityId(), false, $this->getTargetStore()->getId());
    }
}
