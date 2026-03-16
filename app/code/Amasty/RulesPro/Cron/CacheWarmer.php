<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Special Promotions Pro for Magento 2
 */

namespace Amasty\RulesPro\Cron;

use Amasty\RulesPro\Model\Queue\QueueGetList;
use Amasty\RulesPro\Model\Queue\QueueProcessor;
use Amasty\RulesPro\Model\Queue\QueueRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CacheWarmer
{
    private const BATCH_SIZE = 1000;

    /**
     * @var QueueRepository
     */
    private $queueRepository;

    /**
     * @var QueueProcessor
     */
    private $queueProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QueueGetList
     */
    private $queueGetList;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        QueueRepository $queueRepository,
        QueueProcessor $queueProcessor,
        QueueGetList $queueGetList,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->queueRepository = $queueRepository;
        $this->queueProcessor = $queueProcessor;
        $this->queueGetList = $queueGetList;
        $this->searchCriteriaBuilder =  $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $customerIds = [];
        $queueIds = [];
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setPageSize(self::BATCH_SIZE);

        foreach ($this->queueGetList->getList($searchCriteria)->getItems() as $queueEntity) {
           // $customerIds[] = $queueEntity->getCustomerId();
          //  $queueIds[] = $queueEntity->getQueueId();
            // Verify if customer exists before adding to the list

            try {
                $customer = $this->customerRepository->getById($queueEntity->getCustomerId());
                if ($customer->getId()) {
                    $customerIds[] = $queueEntity->getCustomerId();
                    $queueIds[] = $queueEntity->getQueueId();
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // Log the exception or handle it as necessary
            }
        }

        if (count($customerIds) > 0) {
            $this->queueProcessor->process($customerIds);
            $this->queueRepository->deleteByIds($queueIds);
        }
    }
}
