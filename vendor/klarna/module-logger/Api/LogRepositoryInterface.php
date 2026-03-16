<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Logger\Api;

use Klarna\Logger\Api\Data\LogInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface LogRepositoryInterface
{
    /**
     * Save log.
     *
     * @param LogInterface $log
     * @return LogInterface
     * @throws LocalizedException
     */
    public function save(LogInterface $log): LogInterface;

    /**
     * Delete log.
     *
     * @param LogInterface $log
     * @return LogInterface
     * @throws LocalizedException
     */
    public function delete(LogInterface $log): LogInterface;

    /**
     * Delete log by ID
     *
     * @param string $id
     * @return LogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById(string $id): LogInterface;

    /**
     * Retrieve log.
     *
     * @param string $logId
     * @return LogInterface
     * @throws LocalizedException
     */
    public function getById(string $logId): LogInterface;

    /**
     * Get request log list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
