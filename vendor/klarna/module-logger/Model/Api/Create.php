<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\Api;

use Klarna\Logger\Model\Cleanser;
use Klarna\Logger\Model\LogFactory;
use Klarna\Logger\Model\LogRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Klarna\Logger\Model\Logger;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Create
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var LogFactory
     */
    private $logFactory;
    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var Cleanser
     */
    private $cleanser;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ScopeConfigInterface  $config
     * @param StoreManagerInterface $storeManager
     * @param LogFactory            $logFactory
     * @param LogRepository         $logRepository
     * @param Json                  $json
     * @param Cleanser              $cleanser
     * @param Logger                $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager,
        LogFactory $logFactory,
        LogRepository $logRepository,
        Json $json,
        Cleanser $cleanser,
        Logger $logger
    ) {
        $this->config                = $config;
        $this->storeManager          = $storeManager;
        $this->logFactory            = $logFactory;
        $this->logRepository         = $logRepository;
        $this->json                  = $json;
        $this->cleanser              = $cleanser;
        $this->logger                = $logger;
    }

    /**
     * Adding an entry in the database
     *
     * @param Container $loggerContainer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addEntry(Container $loggerContainer): void
    {
        $request  = $loggerContainer->getRequest();
        $response = $loggerContainer->getResponse();

        if (!$this->config->isSetFlag(
            'klarna/api/test_mode',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        )) {
            $request  = $this->anonymizeData($request);
            $response = $this->anonymizeData($response);
        }

        $log = $this->logFactory->create();
        $log->setStatus($response['response_status_code'] ?? Container::DEFAULT_STATUS);
        $log->setAction($loggerContainer->getAction());
        $log->setKlarnaId($loggerContainer->getKlarnaId());
        $log->setIncrementId($loggerContainer->getIncrementId());
        $log->setUrl($loggerContainer->getUrl());
        $log->setMethod($loggerContainer->getMethod());
        $log->setService($loggerContainer->getService());
        $log->setRequest($this->json->serialize($request));
        $log->setResponse($this->json->serialize($response));

        try {
            $this->logRepository->save($log);
        } catch (CouldNotSaveException $e) {
            $this->logger->error('Could not log to the database. Logging to the file');
            $this->logger->logArray($log->getAll());
        }
    }

    /**
     * Anonymize data
     *
     * @param array $data
     * @return array
     */
    private function anonymizeData(array $data): array
    {
        $keys = [
            'billing_address',
            'shipping_address'
        ];
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = $this->cleanser->clean($data[$key]);
            }
        }

        return $data;
    }
}
