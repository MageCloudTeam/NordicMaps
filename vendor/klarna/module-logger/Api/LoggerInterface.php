<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Logger\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface for the Klarna logger class
 *
 * @api
 */
interface LoggerInterface extends \Psr\Log\LoggerInterface
{

    /**
     * Logging a exception
     *
     * @param \Exception $e
     * @param array $context
     * @return LoggerInterface
     */
    public function logException(\Exception $e, array $context = []);

    /**
     * Logging the api request
     *
     * @param array $request
     * @param array $context
     * @return LoggerInterface
     */
    public function logApiRequest(array $request, array $context = []);

    /**
     * Logging the api response
     *
     * @param array $response
     * @param array $context
     * @return LoggerInterface
     */
    public function logApiResponse(array $response, array $context = []);

    /**
     * Logging a array
     *
     * @param array $input
     * @param array $context
     * @return LoggerInterface
     */
    public function logArray(array $input, array $context = []);

    /**
     * Forcing the logging even if its disabled in the configuration
     *
     * @param string $message
     * @param array $context
     * @return LoggerInterface
     */
    public function forceLogging($message, array $context = []);

    /**
     * Setting the request context
     *
     * @param RequestInterface $request
     */
    public function setRequestContext(RequestInterface $request): void;

    /**
     * Setting the Magento order
     *
     * @param OrderInterface $magentoOrder
     */
    public function setMagentoOrder(OrderInterface $magentoOrder): void;
}
