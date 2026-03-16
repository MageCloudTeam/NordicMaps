<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Quote\Inventory;

use Amasty\Shiprestriction\Model\ConfigProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote;

class GetSourceSelectionResultFromQuote
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var InventoryRequestFromQuoteFactory
     */
    private $inventoryRequestFromQuoteFactory;

    /**
     * @var QuoteSourceSelectionResultInterfaceFactory
     */
    private $quoteSourceSelectionResultFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array<int, QuoteSourceSelectionResultInterface>
     */
    private $cachedResults = [];

    /**
     * @var MsiModuleStatusInspector
     */
    private $msiModuleStatusInspector;

    public function __construct(
        ConfigProvider $configProvider,
        InventoryRequestFromQuoteFactory $inventoryRequestFromQuoteFactory,
        QuoteSourceSelectionResultInterfaceFactory $quoteSourceSelectionResultFactory,
        ObjectManagerInterface $objectManager,
        MsiModuleStatusInspector $msiModuleStatusInspector
    ) {
        $this->configProvider = $configProvider;
        $this->inventoryRequestFromQuoteFactory = $inventoryRequestFromQuoteFactory;
        $this->quoteSourceSelectionResultFactory = $quoteSourceSelectionResultFactory;
        $this->objectManager = $objectManager;
        $this->msiModuleStatusInspector = $msiModuleStatusInspector;
    }

    /**
     * @param Quote $quote
     * @param bool $useCache
     * @return QuoteSourceSelectionResultInterface
     * @throws NoSuchEntityException
     */
    public function execute(Quote $quote, bool $useCache = true): QuoteSourceSelectionResultInterface
    {
        if ($this->msiModuleStatusInspector->isEnabled()) {
            $sourceSelectionService = $this->objectManager->create(
                \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface::class
            );
        }

        if ($useCache && $cachedResult = $this->cachedResults[(int) $quote->getId()] ?? null) {
            return $cachedResult;
        }

        $inventoryRequest = $this->inventoryRequestFromQuoteFactory->create($quote);
        $selectionAlgorithmCode = $this->configProvider->getMsiAlgorithm();
        $sourceSelectionResult = $sourceSelectionService->execute($inventoryRequest, $selectionAlgorithmCode);
        $quoteSourceSelectionResult = $this->convertResult($sourceSelectionResult);

        if ($useCache) {
            $this->cachedResults[(int) $quote->getId()] = $quoteSourceSelectionResult;
        }

        return $quoteSourceSelectionResult;
    }

    /**
     * @param \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface $sourceSelectionResult
     * @return QuoteSourceSelectionResultInterface
     */
    private function convertResult(
        \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface $sourceSelectionResult
    ): QuoteSourceSelectionResultInterface {
        $sourceCodes = [];

        foreach ($sourceSelectionResult->getSourceSelectionItems() as $sourceSelectionItem) {
            if ($sourceSelectionItem->getQtyToDeduct()) {
                $sourceCodes[] = $sourceSelectionItem->getSourceCode();
            }
        }

        $sourceCodes = array_unique($sourceCodes);

        return $this->quoteSourceSelectionResultFactory->create()
            ->setSourceCodes($sourceCodes);
    }
}
