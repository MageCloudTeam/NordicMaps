<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Rest\Service;

use Klarna\Base\Api\ServiceInterface;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Base\Helper\VersionInfo;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Model\Api\Rest\Service;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Logger\Model\Api\Container;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Api\ResponseFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Klarna\Base\Exception as KlarnaException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Payments implements CreditApiInterface
{
    public const API_VERSION = 'v1';

    /**
     * @var ServiceInterface
     */
    private $service;
    /**
     * @var VersionInfo
     */
    private $versionInfo;
    /**
     * @var StoreInterface
     */
    private $store;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;
    /**
     * @var Container
     */
    private $loggerContainer;
    /**
     * @var QuoteInterface
     */
    private $klarnaQuote = null;

    /**
     * @param ScopeConfigInterface  $config
     * @param StoreManagerInterface $storeManager
     * @param VersionInfo           $versionInfo
     * @param ResponseFactory       $responseFactory
     * @param KlarnaConfig          $klarnaConfig
     * @param ServiceInterface      $service
     * @param Container|null        $loggerContainer
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        VersionInfo $versionInfo,
        ResponseFactory $responseFactory,
        KlarnaConfig $klarnaConfig,
        ServiceInterface $service,
        Container $loggerContainer
    ) {
        $this->service = $service;
        $this->responseFactory = $responseFactory;
        $this->store = $storeManager->getStore();
        $this->config = $config;
        $this->versionInfo = $versionInfo;
        $this->klarnaConfig = $klarnaConfig;
        $this->loggerContainer = $loggerContainer;
    }

    /**
     * Setting the Klarna quote
     *
     * @param QuoteInterface $klarnaQuote
     */
    public function setKlarnaQuote(QuoteInterface $klarnaQuote): void
    {
        $this->klarnaQuote = $klarnaQuote;
    }

    /**
     * Processing the request
     *
     * @param string                $url
     * @param string                $action
     * @param null|RequestInterface $request
     * @param string                $method
     * @param null|string           $klarnaId
     * @return Response
     * @throws KlarnaException
     */
    private function processRequest(
        string $url,
        string $action,
        RequestInterface $request = null,
        string $method = ServiceInterface::POST,
        string $klarnaId = null
    ) {
        $this->loggerContainer->setAction($action);
        $body = $this->getBody($request);
        $this->connect();
        $response = $this->service->makeRequest($url, ServiceInterface::SERVICE_KP, $body, $method, $klarnaId, $action);
        if (!isset($response['response_status_code'])) {
            throw new KlarnaException(__('The Klarna API request failed because of a timeout.'));
        }

        $response['response_code'] = $response['response_status_code'];
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * Getting back the body
     *
     * @param RequestInterface|null $request
     * @return array
     */
    private function getBody(RequestInterface  $request = null): array
    {
        if ($request) {
            return $request->toArray();
        }

        return [];
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

        $username = $this->config->getValue('klarna/api/merchant_id', ScopeInterface::SCOPE_STORES, $this->store);
        $password = $this->config->getValue('klarna/api/shared_secret', ScopeInterface::SCOPE_STORES, $this->store);
        $test_mode = $this->config->getValue('klarna/api/test_mode', ScopeInterface::SCOPE_STORES, $this->store);

        $versionConfig = $this->klarnaConfig->getVersionConfig($this->store);
        $url = $versionConfig->getUrl($test_mode);

        $this->service->connect($username, $password, $url);
    }

    /**
     * Creating the session
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function createSession(RequestInterface $request)
    {
        return $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions',
            CreditApiInterface::ACTIONS['create_session'],
            $request,
            ServiceInterface::POST,
            null
        );
    }

    /**
     * Updating the session
     *
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function updateSession($sessionId, RequestInterface $request)
    {
        $response = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $sessionId,
            CreditApiInterface::ACTIONS['update_session'],
            $request,
            ServiceInterface::POST,
            $sessionId
        );
        if ($response->getResponseCode() === Service::HTTP_NO_CONTENT) {
            return $this->readSession($sessionId);
        }
        return $response;
    }

    /**
     * Reading the session
     *
     * @param string $sessionId
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function readSession(string $sessionId)
    {
        $resp = $this->processRequest(
            '/payments/' . self::API_VERSION . '/sessions/' . $sessionId,
            CreditApiInterface::ACTIONS['read_session'],
            null,
            ServiceInterface::GET,
            $sessionId
        );
        $response = $resp->toArray();
        $response['session_id'] = $sessionId;
        return $this->responseFactory->create(['data' => $response]);
    }

    /**
     * Placing the order
     *
     * @param string           $authorization_token
     * @param RequestInterface $request
     * @param null|string      $klarnaId
     * @param null|string      $incrementId
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function placeOrder(
        $authorization_token,
        RequestInterface $request,
        $klarnaId = null,
        string $incrementId = null
    ) {
        $this->loggerContainer->setIncrementId($incrementId);

        return $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $authorization_token . '/order',
            CreditApiInterface::ACTIONS['create_order'],
            $request,
            ServiceInterface::POST,
            $klarnaId
        );
    }

    /**
     * Cancelling the order
     *
     * @param string $authorization_token
     * @param null   $klarnaId
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function cancelOrder($authorization_token, $klarnaId = null)
    {
        return $this->processRequest(
            '/payments/' . self::API_VERSION . '/authorizations/' . $authorization_token,
            CreditApiInterface::ACTIONS['cancel_order'],
            null,
            ServiceInterface::DELETE,
            $klarnaId
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
        $version = $versionInfo->getModuleVersionString(
            $versionInfo->getVersion('Klarna_Kp'),
            'Klarna_Kp'
        ) . ';';

        if ($this->klarnaQuote !== null && $this->klarnaQuote->isKecSession()) {
            $version .= 'Magento2_KEC/' . $versionInfo->getVersion('Klarna_Kec') . ';';
        }
        $version .= $versionInfo->getFullM2KlarnaVersion();

        $this->service->setUserAgent('Magento2_KP', $version, $versionInfo->getMageInfo());
    }
}
