<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Observer;

use DateTime;
use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Base\Helper\Debug\StringifyDbTableData;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @internal
 */
class DebugDataCollectorObserver implements ObserverInterface
{
    /**
     * @var StringifyDbTableData
     */
    private StringifyDbTableData $stringifyDbTableData;
    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @param StringifyDbTableData $stringifyDbTableData
     * @param DateTime $dateTime
     * @codeCoverageIgnore
     */
    public function __construct(
        StringifyDbTableData $stringifyDbTableData,
        DateTime $dateTime
    ) {
        $this->stringifyDbTableData = $stringifyDbTableData;
        $this->dateTime = $dateTime;
    }

    /**
     * Collects data from the database and adds it to the debug data object
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->setDataToDataObject($observer->getEvent()->getDebugDataObject());
    }

    /**
     * Adds data from the database to the debug data object
     *
     * @param DebugDataObject $dataObject
     * @return void
     */
    protected function setDataToDataObject(DebugDataObject $dataObject): void
    {
        $tableName = 'klarna_logs';
        $tenDaysAgo = $this
            ->dateTime
            ->setTime(0, 0)
            ->modify('-10 days')
            ->format('Y-m-d H:i:s');

        $klarnaLogTableData = $this->stringifyDbTableData->getStringData($tableName, [
            'conditions' => [['field' => 'created_at >= ?', 'value' => $tenDaysAgo]],
            'orderBy' => 'created_at DESC',
            'limit' => 1000,
        ]);
        $dataObject->addData('klarna_logger', $klarnaLogTableData);
    }
}
