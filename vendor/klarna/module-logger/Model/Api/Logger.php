<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @internal
 */
class Logger
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var Update
     */
    private $loggerCreate;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $config
     * @param Create                $loggerCreate
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        Create $loggerCreate
    ) {
        $this->storeManager = $storeManager;
        $this->config       = $config;
        $this->loggerCreate = $loggerCreate;
    }

    /**
     * Logging the content of the container to the database
     *
     * @param Container $loggerContainer
     * @throws LocalizedException
     */
    public function logContainer(Container $loggerContainer): void
    {
        if (!$this->config->isSetFlag(
            'klarna/api/request_logging',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        )) {
            return;
        }

        $this->loggerCreate->addEntry($loggerContainer);
    }

    /**
     * Logging a callback request
     *
     * @param Container        $container
     * @param string           $action
     * @param RequestInterface $request
     * @param array            $response
     */
    public function logCallback(
        Container $container,
        string $action,
        RequestInterface $request,
        array $response = []
    ): void {
        if (!isset($response['response_status_code'])) {
            $response['response_status_code'] = 200;
        }
        $container->setKlarnaId($request->getParam('id'));

        $this->extendContainer(
            $container,
            $action,
            '/' . str_replace('_', '/', $request->getFullActionName()),
            json_decode($request->getContent(), true) ?? [],
            $response
        );

        $this->logContainer($container);
    }

    /**
     * Logging the callback exception
     *
     * @param Container        $container
     * @param string           $action
     * @param RequestInterface $request
     * @param \Exception       $exception
     */
    public function logCallbackException(
        Container $container,
        string $action,
        RequestInterface $request,
        \Exception $exception
    ): void {
        $response = [
            'code' =>                 $exception->getCode(),
            'message' =>              $exception->getMessage(),
            'file' =>                 $exception->getFile(),
            'line' =>                 $exception->getLine(),
            'trace' =>                $exception->getTraceAsString(),
            'response_status_code' => 400
        ];

        $this->logCallback($container, $action, $request, $response);
    }

    /**
     * Adding some generic information and performing the logging
     *
     * @param Container $container
     * @param string    $action
     * @param string    $url
     * @param array     $request
     * @param array     $response
     */
    private function extendContainer(
        Container $container,
        string $action,
        string $url,
        array $request = [],
        array $response = []
    ): void {
        $container->setAction($action);
        $container->setUrl($url);
        $container->setRequest($request);
        $container->setResponse($response);
        $container->setMethod('post');
    }
}
