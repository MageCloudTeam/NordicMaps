<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\FastOrder\Model\Search;

/**
 * Class Save
 * @package Bss\FastOrder\Model\Search
 */
class Save
{
    /**
     * @var \Bss\FastOrder\Helper\Data
     */
    private $helperBss;

    /**
     * @var \Bss\FastOrder\Helper\HelperSearchSave
     */
    private $helperSave;

    /**
     * @var \Bss\FastOrder\Helper\ConfigurableProduct
     */
    private $configurableProductHelper;

    /**
     * @var \Bss\FastOrder\Controller\Index\Option
     */
    private $optionLayout;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var bool
     */
    private $isPreOrder;

    /**
     * Save constructor.
     * @param \Bss\FastOrder\Helper\Data $helperBss
     * @param \Bss\FastOrder\Helper\HelperSearchSave $helperSave
     * @param \Bss\FastOrder\Helper\ConfigurableProduct $configurableProductHelper
     * @param \Bss\FastOrder\Controller\Index\Option $optionLayout
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Bss\FastOrder\Helper\Data $helperBss,
        \Bss\FastOrder\Helper\HelperSearchSave $helperSave,
        \Bss\FastOrder\Helper\ConfigurableProduct $configurableProductHelper,
        \Bss\FastOrder\Controller\Index\Option $optionLayout,
        \Magento\Framework\Registry $registry
    ) {
        $this->helperBss = $helperBss;
        $this->helperSave = $helperSave;
        $this->configurableProductHelper = $configurableProductHelper;
        $this->optionLayout = $optionLayout;
        $this->registry = $registry;
    }

    /**
     * @return bool|int|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMaxResShow()
    {
        $maxRes = ($this->helperBss->getConfig('max_results_show') > 0) ?
            $this->helperBss->getConfig('max_results_show') : 5;
        return $maxRes;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    private function getShowPopup($product)
    {
        $showPopup = 0;

        if ($product->getBssHidePrice()) {
            return 0;
        }

        if ($product->getHasOptions()) {
            $showPopup = 1;
        }
        if ($product->getTypeId() == 'configurable' || $product->getTypeId() == 'grouped') {
            $showPopup = 1;
        }
        if ($product->getTypeId() == 'downloadable' &&
            $product->getTypeInstance()->getLinkSelectionRequired($product)
        ) {
            $showPopup = 1;
        }
        return $showPopup;
    }

    /**
     * @param $collection
     * @param bool $csv
     * @param string $image
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResData($collection, $csv = false, $image = 'category_page_grid')
    {
        $data = [];
        $this->isPreOrder = $this->helperBss->isPreOrder();
        foreach ($collection as $product) {
            $this->helperBss->getEventManager()->dispatch('bss_prepare_product_price', ['product' => $product]);
            $showPopup = $this->getShowPopup($product);

            if ($showPopup && $csv) {
                continue;
            }

            $data[] = $this->_getProductData(
                $product,
                false,
                $image
            );
        }
        return $data;
    }

    /**
     * @param $product
     * @param string $image
     * @param bool $includePopupHtml
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _getProductData($product, $includePopupHtml = false, $image = 'category_page_grid')
    {
        $productId = $product->getId();
        $productSku = $product->getSku();
        $productType = $product->getTypeId();
        if ($parentProductId = $this->configurableProductHelper->getParentProductId($productId)) {
            // handle for child of configurable product
            $childData = $this->configurableProductHelper->getChildProductData(
                $parentProductId,
                [['sku' => $productSku, 'qty' => 1]]
            );
            if (!empty($childData[0])) {
                return $childData[0];
            }
        } else {
            $showPopup = $this->getShowPopup($product);
            $productName = $product->getName();
            $productUrl = $product->getUrlModel()->getUrl($product);
            $productThumbnail = $this->helperSave->getImageHelper()->init($product, $image)->getUrl();

            $tierPrices = $this->helperBss->getDataTierPrice($product);
            $validators = [];
            $validators['required-number'] = true;
            $stockItem = $this->helperBss->getStockItem($product);
            $params = [];
            $params['minAllowed'] = max((float)$stockItem->getQtyMinAllowed(), 1);
            $this->helperBss->addDataParams($params, $stockItem, $product);
            $validators['validate-item-quantity'] = $params;

            $productPrice = '';
            $productPriceHtml = '';
            $this->helperBss->getPriceHtml($product, $productPriceHtml, $productPrice);

            $productPriceExcTaxHtml = '';
            $productPriceExcTax = '';
            $this->helperBss->getTaxHtml($product, $productPriceExcTaxHtml, $productPriceExcTax);

            $data = [
                'product_name' => $productName,
                'product_sku' => $productSku,
                'product_id' => $productId,
                'product_thumbnail' => $productThumbnail,
                'product_url' => $productUrl,
                'product_type' => $productType,
                'popup' => $showPopup,
                'product_price' => $productPriceHtml,
                'tier_price_' . $productId => $tierPrices,
                'product_price_amount' => $productPrice,
                'data_validate' => $this->helperBss->getJson()->serialize($validators),
                'is_qty_decimal' => (int)$stockItem->getIsQtyDecimal(),
                'product_price_exc_tax_html' => $productPriceExcTaxHtml,
                'product_price_exc_tax' => $productPriceExcTax,
                'pre_order' => $this->isPreOrder
            ];

            if ($showPopup && $includePopupHtml) {
                $this->addPopupHtmlToResult($data, $product);
            }

            return $data;
        }
    }

    /**
     * @param array $data
     * @param \Magento\Catalog\Model\Product $product
     */
    private function addPopupHtmlToResult(&$data, $product)
    {
        $this->registry->unregister('current_product');
        $this->registry->register('current_product', $product);

        $layout = $this->optionLayout->getLayout();
        $layout->getUpdate()->addHandle('default');
        $popupHtml = $layout->createBlock(
            \Bss\FastOrder\Block\FastOrder::class,
            'fastorder.popup.data',
            ['data' => ['is_edit' => false, 'sort_order' => 0]]
        )
            ->setProduct($product)
            ->setTemplate('Bss_FastOrder::option.phtml')
            ->toHtml();

        $data['popup_html'] = $popupHtml;
    }

    /**
     * @param string $sku
     * @param bool $includePopupHtml
     * @return bool|mixed
     */
    public function getProductBySku($sku, $includePopupHtml = false)
    {
        try {
            $product = $this->helperSave->getProductRepositoryInterface()->get($sku);
            return [$this->_getProductData($product, $includePopupHtml)];
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return [];
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            return [];
        }
    }
}
