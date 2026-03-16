<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model;

use Exception;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Logger\Api\LoggerMagentoOrderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Monolog\Handler\HandlerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;

/**
 * Logging information
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @internal
 */
class Logger implements LoggerInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Cleanser
     */
    private $cleanser;
    /**
     * @var HandlerInterface[]
     */
    private $handlers;
    /**
     * @var Level
     */
    private $level;
    /**
     * @var string
     */
    private $name;
    /**
     * @var []
     */
    private $context = [];
    /**
     * @var OrderInterface
     */
    private $magentoOrder;
    /**
     * @var Timezone
     */
    private Timezone $timezone;

    /**
     * @param string                $name
     * @param ScopeConfigInterface  $config
     * @param Cleanser              $cleanser
     * @param StoreManagerInterface $storeManager
     * @param Level                 $level
     * @param Timezone              $timezone
     * @param HandlerInterface[]    $handlers
     * @codeCoverageIgnore
     */
    public function __construct(
        $name,
        ScopeConfigInterface $config,
        Cleanser $cleanser,
        StoreManagerInterface $storeManager,
        Level $level,
        Timezone $timezone,
        array $handlers = []
    ) {
        $this->config       = $config;
        $this->cleanser     = $cleanser;
        $this->storeManager = $storeManager;
        $this->handlers     = $handlers;
        $this->level        = $level;
        $this->name         = $name;
        $this->timezone     = $timezone;
    }

    /**
     * Setting the Magento order
     *
     * @param OrderInterface $magentoOrder
     */
    public function setMagentoOrder(OrderInterface $magentoOrder): void
    {
        $this->magentoOrder = $magentoOrder;
    }

    /**
     * Logging a exception
     *
     * @param Exception $e
     * @param array     $context
     * @return LoggerInterface
     * @throws NoSuchEntityException
     */
    public function logException(Exception $e, array $context = [])
    {
        $input = [
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString()
        ];

        if ($this->isProductionEnvironment()) {
            $input = $this->cleanser->clean($input);
        }

        $logInput = $this->convertToString($input);
        return $this->forceLogging($logInput, $context, $this->level->getCriticalLevel());
    }

    /**
     * Logging the api request
     *
     * @param array $request
     * @param array $context
     * @return bool
     * @throws NoSuchEntityException
     */
    public function logApiRequest(array $request, array $context = [])
    {
        if ($this->isProductionEnvironment()) {
            $request = $this->cleanser->clean($request);
        }

        $logInput = $this->convertToString($request);

        $this->info('API request');
        return $this->addRecord($logInput, $this->level->getDebugLevel(), $context);
    }

    /**
     * Logging the api response
     *
     * @param array $response
     * @param array $context
     * @return bool
     * @throws NoSuchEntityException
     */
    public function logApiResponse(array $response, array $context = [])
    {
        if ($this->isProductionEnvironment()) {
            $response = $this->cleanser->clean($response);
        }

        $logInput = $this->convertToString($response);

        $this->info('API response');
        return $this->addRecord($logInput, $this->level->getDebugLevel(), $context);
    }

    /**
     * @inheritdoc
     */
    public function logArray(array $input, array $context = [])
    {
        if ($this->isProductionEnvironment()) {
            $input = $this->cleanser->clean($input);
        }

        $logInput = $this->convertToString($input);
        return $this->addRecord($logInput, $this->level->getDebugLevel(), $context);
    }

    /**
     * Convert the input into a string
     *
     * @param mixed $input
     * @return string
     */
    private function convertToString($input)
    {
        $result = json_encode($input);
        return str_replace('\n', '', $result);
    }

    /**
     * @inheritdoc
     */
    public function forceLogging($message, array $context = [], $level = 200)
    {
        if (empty($context)) {
            $context = $this->context;
        }

        $input = $this->getHandlerInput($message, $level, $context);
        foreach ($this->handlers as $handler) {
            $handler->handle($input);
        }

        return $this;
    }

    /**
     * Logging of emergency information
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function emergency($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getEmergencyLevel(), $context);
    }

    /**
     * Logging of alerts
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function alert($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getAlertLevel(), $context);
    }

    /**
     * Logging of critical errors
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function critical($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getCriticalLevel(), $context);
    }

    /**
     * Logging of normal errors
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function error($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getErrorLevel(), $context);
    }

    /**
     * Logging of warnings
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function warning($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getWarningLevel(), $context);
    }

    /**
     * Logging of notice information
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function notice($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getNoticeLevel(), $context);
    }

    /**
     * Logging of general information
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function info($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getInfoLevel(), $context);
    }

    /**
     * Logging of debugging information
     *
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function debug($message, array $context = []): void
    {
        $this->addRecord($message, $this->level->getDebugLevel(), $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     * @throws NoSuchEntityException
     */
    public function log($level, $message, array $context = []): void
    {
        if ($level === 'debug') {
            $logLevel = $this->level->getDebugLevel();
            $this->addRecord($message, $logLevel, $context);
        }

        if ($level === 'error') {
            $logLevel = $this->level->getErrorLevel();
            $this->addRecord($message, $logLevel, $context);
        }

        $logLevel = $this->level->getInfoLevel();
        $this->addRecord($message, $logLevel, $context);
    }

    /**
     * Checking if logging is enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isDebugFlagEnabled()
    {
        $store = $this->storeManager->getStore();
        return $this->config->isSetFlag(
            'klarna/api/debug',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Checking if we are in a production environment
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isProductionEnvironment()
    {
        $store = $this->storeManager->getStore();
        return !$this->config->isSetFlag('klarna/api/test_mode', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Adding a log record
     *
     * @param string $message
     * @param int    $level
     * @param array  $context
     * @return bool
     * @throws NoSuchEntityException
     */
    private function addRecord($message, $level, array $context = [])
    {
        if ($this->magentoOrder !== null) {
            $this->magentoOrder->addCommentToStatusHistory($message);
        }

        if (!$this->isDebugFlagEnabled()) {
            return false;
        }

        if (empty($context)) {
            $context = $this->context;
        }

        $input = $this->getHandlerInput($message, $level, $context);

        foreach ($this->handlers as $handler) {
            if (!$handler->handle($input)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Getting the input for the handlers
     *
     * @param string $message
     * @param int    $level
     * @param array  $context
     * @return array
     * @throws Exception
     */
    private function getHandlerInput($message, $level, array $context)
    {
        $levelNames = $this->level->getLevelNames();
        $levelName  = $levelNames[$level];

        return [
            'message'    => $message,
            'context'    => $context,
            'level'      => $level,
            'level_name' => $levelName,
            'extra'      => [],
            'datetime'   => $this->timezone->date(null, null, false),
            'channel'    => $this->name
        ];
    }

    /**
     * @inheritdoc
     */
    public function setRequestContext(RequestInterface $request): void
    {
        $this->context['action'] = $request->getRequestUri();
        $this->context['klarna_id'] = $request->getParam('id');
    }
}
