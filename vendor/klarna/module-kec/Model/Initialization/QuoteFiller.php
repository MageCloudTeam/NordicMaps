<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Initialization;

use Klarna\Base\Model\Quote\ShippingMethod\QuoteMethodHandler;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class QuoteFiller
{
    /**
     * @var QuoteMethodHandler
     */
    private QuoteMethodHandler $quoteMethodHandler;
    /**
     * @var DirectoryHelper
     */
    private DirectoryHelper $directoryHelper;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param QuoteMethodHandler $quoteMethodHandler
     * @param DirectoryHelper $directoryHelper
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteMethodHandler $quoteMethodHandler,
        DirectoryHelper $directoryHelper,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->quoteMethodHandler = $quoteMethodHandler;
        $this->directoryHelper = $directoryHelper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Filling a existing Magento quote with information
     *
     * @param CartInterface $magentoQuote
     * @param array $parameter
     * @return CartInterface
     */
    public function fillExistingMagentoQuote(CartInterface $magentoQuote, array $parameter): CartInterface
    {
        if ($magentoQuote->isVirtual()) {
            return $magentoQuote;
        }

        $shippingAddress = $magentoQuote->getShippingAddress();

        if (isset($parameter['country_id'])) {
            $shippingAddress->setCountryId($parameter['country_id']);
        } else {
            $shippingAddress->setCountryId($this->directoryHelper->getDefaultCountry($magentoQuote->getStore()));
        }

        if (isset($parameter['shipping_method'], $parameter['shipping_carrier_code'])) {
            $this->quoteMethodHandler->setShippingMethod(
                $magentoQuote,
                $parameter['shipping_carrier_code'] . '_' . $parameter['shipping_method']
            );
        } else {
            $this->quoteMethodHandler->setDefaultShippingMethod($magentoQuote);
        }

        return $magentoQuote;
    }

    /**
     * Filling a empty Magento quote
     *
     * @param CartInterface $magentoQuote
     * @param array $parameter
     * @return CartInterface
     */
    public function fillEmptyMagentoQuote(CartInterface $magentoQuote, array $parameter): CartInterface
    {
        $this->addProduct($magentoQuote, $parameter);

        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $magentoQuote->setBaseCurrencyCode($currency);
        $magentoQuote->setGlobalCurrencyCode($currency);
        $magentoQuote->setQuoteCurrencyCode($currency);
        $magentoQuote->setStoreCurrencyCode($currency);
        $magentoQuote->setStore($this->storeManager->getStore());

        if (!$magentoQuote->isVirtual()) {
            $shippingAddress = $magentoQuote->getShippingAddress();
            $shippingAddress->setCountryId($this->directoryHelper->getDefaultCountry($magentoQuote->getStore()));

            $shippingAddress->setCollectShippingRates(true);
            $shippingAddress->collectShippingRates();
            $shippingRates = $shippingAddress->getGroupedAllShippingRates();
            $rates = reset($shippingRates);
            $rate = reset($rates);
            $this->quoteMethodHandler->setShippingMethod($magentoQuote, $rate->getCode());
        } else {
            $billingAddress = $magentoQuote->getBillingAddress();
            $billingAddress->setCountryId($this->directoryHelper->getDefaultCountry($magentoQuote->getStore()));

            $billingAddress->setCollectShippingRates(true);
            $billingAddress->collectShippingRates();
        }

        $customerIsGuest = 1;
        if ($this->customerSession->isLoggedIn()) {
            $customerIsGuest = 0;
            $magentoQuote->assignCustomer($this->customerSession->getCustomerData());
        }
        $magentoQuote->setCustomerIsGuest($customerIsGuest);

        return $magentoQuote;
    }

    /**
     * Adding the product ot the quote
     *
     * @param CartInterface $magentoQuote
     * @param array $parameter
     */
    private function addProduct(CartInterface $magentoQuote, array $parameter): void
    {
        $product = $this->productRepository->getById($parameter['product']);
        $magentoQuote->addProduct($product, $this->dataObjectFactory->create(['data' => $parameter]));
    }
}
