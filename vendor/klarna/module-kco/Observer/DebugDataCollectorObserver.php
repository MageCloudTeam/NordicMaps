<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Observer;

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
     * @param StringifyDbTableData $stringifyDbTableData
     * @codeCoverageIgnore
     */
    public function __construct(
        StringifyDbTableData $stringifyDbTableData,
    ) {
        $this->stringifyDbTableData = $stringifyDbTableData;
    }

    /**
     * Collect data from the database and add it to the debug data object
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->setDataToDataObject($observer->getEvent()->getDebugDataObject());
    }

    /**
     * Set data to data object
     *
     * @param DebugDataObject $dataObject
     * @return void
     */
    protected function setDataToDataObject(DebugDataObject $dataObject): void
    {
        $tableName = 'klarna_kco_quote';
        $kcoQuoteTableData = $this->stringifyDbTableData->getStringData($tableName, [
            'orderBy' => 'kco_quote_id DESC',
            'limit' => 1000,
        ]);
        $dataObject->addData('klarna_kco', $kcoQuoteTableData);
    }
}
