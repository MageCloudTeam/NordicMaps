<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model;

use Klarna\Logger\Api\Data\LogInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Klarna\Logger\Model\ResourceModel\Log as ResourceModel;

/**
 * @internal
 */
class Log extends AbstractModel implements IdentityInterface, LogInterface
{
    public const CACHE_TAG = 'klarna_log';

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get log ID.
     *
     * @return int
     */
    public function getLogId(): int
    {
        return (int) $this->getData('log_id');
    }

    /**
     * Set log ID.
     *
     * @param int $id
     * @return LogInterface $this
     */
    public function setLogId(int $id): LogInterface
    {
        return $this->setData('log_id', $id);
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->getData('status');
    }

    /**
     * Set status.
     *
     * @param int $status
     * @return LogInterface $this
     */
    public function setStatus(int $status): LogInterface
    {
        return $this->setData('status', $status);
    }

    /**
     * Get Action.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->getData('action');
    }

    /**
     * Set action.
     *
     * @param string $action
     * @return LogInterface $this
     */
    public function setAction(string $action): LogInterface
    {
        return $this->setData('action', $action);
    }

    /**
     * Get Klarna ID.
     *
     * @return string|null
     */
    public function getKlarnaId(): ?string
    {
        return $this->getData('klarna_id');
    }

    /**
     * Set Klarna ID.
     *
     * @param string|null $klarnaId
     * @return LogInterface $this
     */
    public function setKlarnaId(?string $klarnaId): LogInterface
    {
        return $this->setData('klarna_id', $klarnaId);
    }

    /**
     * Get Url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getData('url');
    }

    /**
     * Set Url.
     *
     * @param string $url
     * @return LogInterface $this
     */
    public function setUrl(string $url): LogInterface
    {
        return $this->setData('url', $url);
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->getData('method');
    }

    /**
     * Set method.
     *
     * @param string $method
     * @return LogInterface $this
     */
    public function setMethod(string $method): LogInterface
    {
        return $this->setData('method', $method);
    }

    /**
     * Get services.
     *
     * @return string
     */
    public function getService(): string
    {
        return $this->getData('service');
    }

    /**
     * Set service.
     *
     * @param string $service
     * @return LogInterface $this
     */
    public function setService(string $service): LogInterface
    {
        return $this->setData('service', $service);
    }

    /**
     * Get request.
     *
     * @return string
     */
    public function getRequest(): string
    {
        return $this->getData('request');
    }

    /**
     * Set request.
     *
     * @param string $request
     * @return LogInterface $this
     */
    public function setRequest(string $request): LogInterface
    {
        return $this->setData('request', $request);
    }

    /**
     * Get response.
     *
     * @return string
     */
    public function getResponse(): string
    {
        return $this->getData('response');
    }

    /**
     * Set response.
     *
     * @param string $response
     * @return LogInterface $this
     */
    public function setResponse(string $response): LogInterface
    {
        return $this->setData('response', $response);
    }

    /**
     * Get created at.
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData('created_at');
    }

    /**
     * Set created at.
     *
     * @param string $createdAt
     * @return LogInterface $this
     */
    public function setCreatedAt(string $createdAt): LogInterface
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * Setting the increment id
     *
     * @param string|null $incrementId
     * @return LogInterface
     */
    public function setIncrementId(string $incrementId = null): LogInterface
    {
        return $this->setData('increment_id', $incrementId);
    }

    /**
     * Getting back the increment id
     *
     * @return null|string
     */
    public function getIncrementId(): ?string
    {
        return $this->getData('increment_id');
    }
}
