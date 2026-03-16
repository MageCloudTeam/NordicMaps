<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Api;

use Klarna\Kco\Api\ApiInterface;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Helper\KlarnaConfig;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @api
 */
class Factory
{
    public const ERROR_MESSAGES_KEY = 'klarna_kco_messages';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /** @var KlarnaConfig $klarnaConfig */
    private $klarnaConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param KlarnaConfig                              $klarnaConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        KlarnaConfig $klarnaConfig
    ) {
        $this->objectManager = $objectManager;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Get Api instance
     *
     * @param StoreInterface $store
     *
     * @return ApiInterface
     * @throws KlarnaException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createApiInstance(StoreInterface $store = null)
    {
        $versionConfig = $this->klarnaConfig->getVersionConfig($store);

        $typeConfig = $this->klarnaConfig->getApiTypeConfig($versionConfig->getType());

        /** @var ApiInterface $instance */
        $instance = $this->createModel($typeConfig['class']);

        $instance->setStore($store);
        $instance->setConfig($versionConfig);

        return $instance;
    }

    /**
     * Creates new instances of API models
     *
     * @param string $className
     * @return \Klarna\Kco\Api\ApiInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createModel($className)
    {
        $method = $this->objectManager->get($className);
        if (!$method instanceof \Klarna\Kco\Api\ApiInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Klarna\Kco\Api\ApiInterface', $className)
            );
        }
        return $method;
    }
}
