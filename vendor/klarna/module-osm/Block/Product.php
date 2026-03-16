<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Onsitemessaging\Block;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Pricing\Adjustment\Calculator;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data as TaxHelper;

/**
 * @internal
 */
class Product extends Template
{
    /**
     * @var Data
     */
    private $productHelper;
    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private $localeResolver;
    /**
     * @var Calculator
     */
    private $calculator;
    /**
     * @var TaxHelper
     */
    private $taxHelper;

    /**
     * @param Context $context
     * @param MagentoToKlarnaLocaleMapper $locale
     * @param Data $productHelper
     * @param Calculator $calculator
     * @param TaxHelper $taxHelper
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context                     $context,
        MagentoToKlarnaLocaleMapper $locale,
        Data                        $productHelper,
        Calculator                  $calculator,
        TaxHelper                   $taxHelper,
        array                       $data = []
    ) {
        parent::__construct($context, $data);
        $this->localeResolver = $locale;
        $this->productHelper = $productHelper;
        $this->calculator = $calculator;
        $this->taxHelper = $taxHelper;
    }

    /**
     * Check to see if display on product is enabled or not
     *
     * @return bool
     */
    public function showOnProduct(): bool
    {
        return $this->isSetFlag('klarna/osm/enabled')
            && $this->isSetFlag('klarna/osm/product_enabled');
    }

    /**
     * Get the locale according to ISO_3166-1 standard
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Get placement id
     *
     * @return string
     */
    public function getPlacementId(): string
    {
        $placementId = $this->getValue('klarna/osm/product_placement_select');
        if ($placementId && $placementId === 'other') {
            $placementId = $this->getValue('klarna/osm/product_placement_other');
        }
        return $placementId;
    }

    /**
     * Get theme ("", "default" or "dark")
     *
     * @return string
     */
    public function getTheme(): string
    {
        return (string) $this->getValue('klarna/osm/theme');
    }

    /**
     * Get the amount of the purchase formated as an integer `round(amount * 100)`
     *
     * @return int
     */
    public function getPurchaseAmount(): int
    {
        $product = $this->productHelper->getProduct();
        $productPrice   = $product->getFinalPrice($product->getQty());

        $amount = $this->calculator->getAmount($productPrice, $product);
        $price = $amount->getValue();
        if ($this->taxHelper->displayPriceExcludingTax()) {
            $price = $amount->getBaseAmount();
        }
        return (int)round($price * 100);
    }

    /**
     * Wrapper around `$this->_scopeConfig->isSetFlag` that ensures store scope is checked
     *
     * @param string $path
     * @return bool
     */
    private function isSetFlag(string $path): bool
    {
        return $this->_scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
    }

    /**
     * Wrapper around `$this->_scopeConfig->getValue` that ensures store scope is checked
     *
     * @param string $path
     * @return mixed
     */
    private function getValue(string $path)
    {
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
    }
}
