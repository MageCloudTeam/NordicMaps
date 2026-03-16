<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Backend\Model\Api;

use Klarna\Base\Helper\KlarnaConfig;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Backend\Api\ApiInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Creating om api objects and returning them
 *
 * @api
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param KlarnaConfig           $klarnaConfig
     * @codeCoverageIgnore
     */
    public function __construct(ObjectManagerInterface $objectManager, KlarnaConfig $klarnaConfig)
    {
        $this->objectManager = $objectManager;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Creates new instances of API models
     *
     * @param string $className
     * @return ApiInterface
     * @throws LocalizedException
     */
    public function create($className)
    {
        $method = $this->objectManager->create($className);
        if (!$method instanceof \Klarna\Backend\Api\ApiInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Klarna\Backend\Api\ApiInterface', $className)
            );
        }
        return $method;
    }

    /**
     * Creating and returning the ordermanagement api instance
     *
     * @param string         $methodCode
     * @param StoreInterface $store
     * @return ApiInterface
     * @throws LocalizedException
     */
    public function createOmApi(string $methodCode, StoreInterface $store = null): ApiInterface
    {
        $omClass         = $this->klarnaConfig->getOrderMangagementClass($store);
        $orderManagement = $this->create($omClass);
        $orderManagement->resetForStore($store, $methodCode);

        return $orderManagement;
    }
}
