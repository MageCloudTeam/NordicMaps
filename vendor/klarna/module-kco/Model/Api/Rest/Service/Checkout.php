<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Api\Rest\Service;

use Klarna\Base\Api\ServiceInterface;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Base\Helper\VersionInfo;
use Klarna\Kco\Api\ApiInterface;
use Klarna\Kco\Api\KasperInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Klarna\Base\Exception as KlarnaException;

/**
 * @internal
 */
class Checkout implements KasperInterface
{
    public const API_VERSION = 'v3';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var VersionInfo
     */
    private $versionInfo;
    /**
     * @var ServiceInterface
     */
    private $service;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;

    /**
     * Initialize class
     *
     * @param ServiceInterface      $service
     * @param KlarnaConfig          $klarnaConfig
     * @param VersionInfo           $versionInfo
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $config
     * @codeCoverageIgnore
     */
    public function __construct(
        ServiceInterface $service,
        KlarnaConfig $klarnaConfig,
        VersionInfo $versionInfo,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config
    ) {
        $this->service = $service;
        $this->storeManager = $storeManager;
        $this->klarnaConfig = $klarnaConfig;
        $this->versionInfo = $versionInfo;
        $this->config = $config;
    }

    /**
     * Performing the connection
     *
     * @throws KlarnaException
     */
    private function connect()
    {
        $this->setUserAgent($this->versionInfo);
        $this->service->setHeader('Accept', '*/*');

        $store = $this->storeManager->getStore();
        $username = $this->config->getValue('klarna/api/merchant_id', ScopeInterface::SCOPE_STORES, $store);
        $password = $this->config->getValue('klarna/api/shared_secret', ScopeInterface::SCOPE_STORES, $store);
        $test_mode = $this->config->getValue('klarna/api/test_mode', ScopeInterface::SCOPE_STORES, $store);

        $versionConfig = $this->klarnaConfig->getVersionConfig($store);
        $url = $versionConfig->getUrl($test_mode);

        $this->service->connect($username, $password, $url);
    }

    /**
     * Get Klarna order details
     *
     * @param string $id
     * @return array
     * @throws KlarnaException
     */
    public function getOrder(string $id): array
    {
        $this->connect();

        $url = "{$this->uri}/checkout/" . self::API_VERSION . "/orders/{$id}";
        return $this->sendRequest($url, [], 'get_order', ServiceInterface::GET, $id);
    }

    /**
     * Create new order
     *
     * @param array $data
     * @return array
     * @throws \Klarna\Base\Exception
     */
    public function createOrder(array $data): array
    {
        $this->connect();

        $url = "{$this->uri}/checkout/" . self::API_VERSION . "/orders";
        return $this->sendRequest($url, $data, 'create_order', ServiceInterface::POST);
    }

    /**
     * Update Klarna order
     *
     * @param string $id
     * @param array  $data
     * @return array
     * @throws KlarnaException
     */
    public function updateOrder(string $id, array $data): array
    {
        $this->connect();

        $url = "{$this->uri}/checkout/" . self::API_VERSION . "/orders/{$id}";
        return $this->sendRequest($url, $data, 'update_order', ServiceInterface::POST, $id);
    }

    /**
     * Sending the request and returning the response
     *
     * @param string $url
     * @param array $data
     * @param string $actionType
     * @param string $httpMethod
     * @param string $id
     * @return array
     */
    private function sendRequest(
        string $url,
        array $data,
        string $actionType,
        string $httpMethod,
        string $id = null
    ): array {
        return $this->service->makeRequest(
            $url,
            ServiceInterface::SERVICE_KCO,
            $data,
            $httpMethod,
            $id,
            ApiInterface::ACTIONS[$actionType]
        );
    }

    /**
     * Set the service User-Agent
     *
     * @param VersionInfo $versionInfo
     * @return void
     */
    private function setUserAgent(VersionInfo $versionInfo): void
    {
        $version = sprintf(
            '%s;%s;Core/%s;OM/%s',
            $versionInfo->getVersion('Klarna_Kco'),
            $versionInfo->getVersion('Klarna_Base'),
            $versionInfo->getVersion('Klarna_Backend'),
            $versionInfo->getFullM2KlarnaVersion()
        );
        $mageInfo = $versionInfo->getMageInfo();
        $this->service->setUserAgent('Magento2_KCO', $version, $mageInfo);
    }
}
