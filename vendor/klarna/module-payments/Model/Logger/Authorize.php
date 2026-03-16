<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Logger;

use Klarna\Base\Api\ServiceInterface;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Logger\Model\Api\Container;
use Klarna\Logger\Model\Api\Logger;
use Magento\Framework\App\RequestInterface;

/**
 * @internal
 */
class Authorize
{
    /**
     * @var Container
     */
    private Container $container;
    /**
     * @var Logger
     */
    private Logger $apiLogger;

    /**
     * @param Container $container
     * @param Logger $apiLogger
     * @codeCoverageIgnore
     */
    public function __construct(Container $container, Logger $apiLogger)
    {
        $this->container = $container;
        $this->apiLogger = $apiLogger;
    }

    /**
     * Logging the authorize request
     *
     * @param string $sessionId
     * @param RequestInterface $request
     */
    public function logRequest(string $sessionId, RequestInterface $request): void
    {
        $this->setContainerValues($sessionId, $request);
        $this->apiLogger->logContainer($this->container);
    }

    /**
     * Logging the exception
     *
     * @param string $sessionId
     * @param RequestInterface $request
     * @param \Exception $exception
     */
    public function logException(string $sessionId, RequestInterface $request, \Exception $exception): void
    {
        $this->setContainerValues($sessionId, $request);
        $response = [
            'code' =>                 $exception->getCode(),
            'message' =>              $exception->getMessage(),
            'file' =>                 $exception->getFile(),
            'line' =>                 $exception->getLine(),
            'trace' =>                $exception->getTraceAsString(),
            'response_status_code' => 400
        ];
        $this->container->setResponse($response);
        $this->apiLogger->logContainer($this->container);
    }

    /**
     * Setting the container values
     *
     * @param string $sessionId
     * @param RequestInterface $request
     */
    private function setContainerValues(string $sessionId, RequestInterface $request): void
    {
        $this->container->setService(ServiceInterface::SERVICE_KP);
        $this->container->setKlarnaId($sessionId);
        $this->container->setAction(CreditApiInterface::ACTIONS['authorize_callback']);
        $this->container->setRequest(json_decode($request->getContent(), true));
        $this->container->setUrl('/' . str_replace('_', '/', $request->getFullActionName()));
        $this->container->setMethod('post');
        $this->container->setResponse([]);
    }
}
