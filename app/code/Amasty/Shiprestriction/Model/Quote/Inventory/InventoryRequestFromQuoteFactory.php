<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Quote\Inventory;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestExtensionInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;

class InventoryRequestFromQuoteFactory
{
    /**
     * @var InventoryRequestInterfaceFactory|null
     */
    private $inventoryRequestFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockByWebsiteIdResolverInterface|null
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var ItemRequestInterfaceFactory|null
     */
    private $itemRequestFactory;

    /**
     * @var AddressInterfaceFactory|null
     */
    private $inventorySourceSelectionAddressFactory;

    /**
     * @var InventoryRequestExtensionInterfaceFactory|null
     */
    private $inventoryRequestExtensionFactory;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface|null
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var GetStockItemConfigurationInterface|null
     */
    private $getStockItemConfiguration;

    public function __construct(
        StoreManagerInterface $storeManager,
        MsiModuleStatusInspector $msiModuleStatusInspector,
        ObjectManagerInterface $objectManager
    ) {
        $this->storeManager = $storeManager;
        if ($msiModuleStatusInspector->isEnabled()) {
            $this->inventoryRequestFactory = $objectManager->create(InventoryRequestInterfaceFactory::class);
            $this->stockByWebsiteIdResolver = $objectManager->create(StockByWebsiteIdResolverInterface::class);
            $this->itemRequestFactory = $objectManager->create(ItemRequestInterfaceFactory::class);
            $this->inventorySourceSelectionAddressFactory = $objectManager->create(AddressInterfaceFactory::class);
            $this->inventoryRequestExtensionFactory = $objectManager->create(
                InventoryRequestExtensionInterfaceFactory::class
            );
            $this->isSourceItemManagementAllowedForProductType = $objectManager->create(
                IsSourceItemManagementAllowedForProductTypeInterface::class
            );
            $this->getStockItemConfiguration = $objectManager->create(GetStockItemConfigurationInterface::class);
        }
    }

    /**
     * @param Quote $quote
     * @return InventoryRequestInterface
     * @throws NoSuchEntityException
     */
    public function create(Quote $quote): InventoryRequestInterface
    {
        $store = $this->storeManager->getStore($quote->getStoreId());
        $stock = $this->stockByWebsiteIdResolver->execute((int) $store->getWebsiteId());

        $inventoryRequest = $this->inventoryRequestFactory->create([
            'stockId'   => $stock->getStockId(),
            'items'     => $this->getRequestItems($quote->getAllItems(), $stock->getStockId())
        ]);

        $address = $this->getAddressFromQuote($quote);
        if ($address !== null) {
            $extensionAttributes = $this->inventoryRequestExtensionFactory->create();
            $extensionAttributes->setDestinationAddress($address);
            $inventoryRequest->setExtensionAttributes($extensionAttributes);
        }

        return $inventoryRequest;
    }

    /**
     * @param QuoteItem[] $quoteItems
     * @param int $stockId
     * @return ItemRequestInterface[]
     */
    private function getRequestItems(array $quoteItems, int $stockId): array
    {
        $requestItems = [];

        foreach ($quoteItems as $quoteItem) {
            $stockConfiguration = $this->getStockItemConfiguration->execute($quoteItem->getSku(), $stockId);

            if (!$stockConfiguration->isManageStock()
                || !$this->isSourceItemManagementAllowedForProductType->execute($quoteItem->getRealProductType())
            ) {
                continue;
            }

            $requestItems[] = $this->itemRequestFactory->create([
                'sku' => $quoteItem->getSku(),
                'qty' => $this->castQty($quoteItem, $quoteItem->getQty())
            ]);
        }

        return $requestItems;
    }

    /**
     * Cast qty value
     *
     * @param QuoteItem $item
     * @param string|int|float $qty
     * @see \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice::castQty
     * @return float
     */
    private function castQty(QuoteItem $item, $qty): float
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

        return $qty > 0 ? $qty : 0;
    }

    /**
     * @param Quote $quote
     * @return AddressInterface|null
     */
    private function getAddressFromQuote(Quote $quote): ?AddressInterface
    {
        $shippingAddress = $quote->getShippingAddress();

        if ($shippingAddress === null) {
            return null;
        }

        return $this->inventorySourceSelectionAddressFactory->create([
            'country'   => $shippingAddress->getCountryId(),
            'postcode'  => $shippingAddress->getPostcode() ?? '',
            'street'    => implode("\n", $shippingAddress->getStreet()),
            'region'    => $shippingAddress->getRegionCode() ?? '',
            'city'      => $shippingAddress->getCity() ?? ''
        ]);
    }
}
