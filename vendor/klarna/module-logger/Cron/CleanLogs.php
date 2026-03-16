<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Cron;

use Klarna\Logger\Model\LogRepository;
use Klarna\Logger\Model\ResourceModel\Log\Collection as LogCollection;
use Klarna\Logger\Model\ResourceModel\Log\CollectionFactory as LogCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
class CleanLogs
{
    public const SECONDSINDAY = 86400;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $config;
    /**
     * @var LogCollection
     */
    private LogCollection $logCollection;
    /**
     * @var LogRepository
     */
    private LogRepository $logRepository;
    /**
     * @var LogCollectionFactory
     */
    private LogCollectionFactory $logCollectionFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var string
     */
    private string $logLifetime = 'klarna/api/delete_request_logs_after';

    /**
     * @param ScopeConfigInterface  $config
     * @param StoreManagerInterface $storeManager
     * @param LogCollection         $logCollection
     * @param LogRepository         $logRepository
     * @param LogCollectionFactory  $logCollectionFactory
     * @param LoggerInterface       $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface  $config,
        StoreManagerInterface $storeManager,
        LogCollection         $logCollection,
        LogRepository         $logRepository,
        LogCollectionFactory  $logCollectionFactory,
        LoggerInterface       $logger
    ) {
        $this->config               = $config;
        $this->storeManager         = $storeManager;
        $this->logCollection        = $logCollection;
        $this->logRepository        = $logRepository;
        $this->logCollectionFactory = $logCollectionFactory;
        $this->logger               = $logger;
    }

    /**
     * Clean expired logs (cron process).
     *
     * @return void
     */
    public function execute(): void
    {
        $logCollection = $this->getLogs();
        $logCollection->setPageSize(50);
        $lastPage = $logCollection->getSize() ? $logCollection->getLastPageNumber() : 0;

        for ($currentPage = $lastPage; $currentPage >= 1; $currentPage--) {
            $logCollection->setCurPage($currentPage);
            $logCollection->walk('delete');
            $logCollection->clear();
        }
    }

    /**
     * Gets logs.
     *
     * Log is considered expired if the created_at date
     * of the entry is greater than lifetime threshold
     *
     * @return LogCollection
     */
    private function getLogs(): LogCollection
    {
        $lifetime = $this->config->getValue(
            $this->logLifetime,
            ScopeInterface::SCOPE_WEBSITE
        );
        $lifetime *= self::SECONDSINDAY;

        $logs = $this->logCollectionFactory->create();
        $logs->addFieldToFilter('created_at', ['to' => date("Y-m-d", time() - $lifetime)]);
        $logs->addFieldToSelect('log_id');

        return $logs;
    }
}
