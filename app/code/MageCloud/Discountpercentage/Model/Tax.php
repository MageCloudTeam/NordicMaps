<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Discountpercentage\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Api\TaxCalculationInterface;

/**
 * Class Tax
 */
class Tax
{
    /**
     * @var TaxCalculationInterface
     */
    private $taxCalculation;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor call
     *
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TaxCalculationInterface $taxCalculation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TaxCalculationInterface $taxCalculation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     * @throws LocalizedException
     */
    public function getPriceInclAndExclTax(ProductInterface $product): array
    {
        $priceIncludingTax = $priceExcludingTax = $product->getPrice();

        if ($taxAttribute = $product->getCustomAttribute('tax_class_id')) {
            $productRateId = $taxAttribute->getValue();
            $rate = $this->taxCalculation->getCalculatedRate($productRateId);

            if ((int)$this->scopeConfig->getValue('tax/calculation/price_includes_tax', ScopeInterface::SCOPE_STORE) === 1) {
                $priceExcludingTax = $product->getPrice() / (1 + ($rate / 100));
            }

            $priceIncludingTax = $priceExcludingTax + ($priceExcludingTax * ($rate / 100));
        }

        return [
            'incl' => $priceIncludingTax,
            'excl' => $priceExcludingTax,
        ];
    }
}