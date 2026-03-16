<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model\Page;

use Kartbutikken\WebsiteLocator\Api\UrlRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Store\Api\Data\StoreInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class AbstractPage
 */
abstract class AbstractPage extends DataObject implements TypeInterface
{
    /**
     * @var UrlRepositoryInterface
     */
    private $urlRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreInterface
     */
    private $targetStore;

    /**
     * AbstractPage constructor.
     *
     * @param UrlRepositoryInterface $urlRepository
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        UrlRepositoryInterface $urlRepository,
        RequestInterface $request,
        array $data = []
    ) {
        $this->urlRepository = $urlRepository;
        $this->request = $request;

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getRealUrl(): string
    {
        if($this->isEnabled()) {
            return $this->getUrl();
        }

        return $this->getTargetStore()->getBaseUrl();
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->_getData(UrlRewrite::ENTITY_ID);
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(UrlRewrite::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->_getData(UrlRewrite::ENTITY_TYPE);
    }

    /**
     * @param string $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->setData(UrlRewrite::ENTITY_TYPE, $entityType);
    }

    /**
     * @return string
     */
    public function getRequestPath()
    {
        return $this->_getData(UrlRewrite::REQUEST_PATH);
    }

    /**
     * @param string $requestPath
     *
     * @return $this
     */
    public function setRequestPath($requestPath)
    {
        return $this->setData(UrlRewrite::REQUEST_PATH, $requestPath);
    }

    /**
     * @return string
     */
    public function getTargetPath()
    {
        return $this->_getData(UrlRewrite::TARGET_PATH);
    }

    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function setTargetPath($targetPath)
    {
        return $this->setData(UrlRewrite::TARGET_PATH, $targetPath);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(UrlRewrite::STORE_ID);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(UrlRewrite::STORE_ID, $storeId);
    }

    /**
     * @return StoreInterface
     */
    public function getTargetStore(): StoreInterface
    {
        return $this->targetStore;
    }

    /**
     * @param StoreInterface $targetStore
     *
     * @return $this
     */
    public function setTargetStore(StoreInterface $targetStore)
    {
        $this->targetStore = $targetStore;

        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
