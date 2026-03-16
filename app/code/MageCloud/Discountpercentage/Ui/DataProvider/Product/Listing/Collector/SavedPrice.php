<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Discountpercentage\Ui\DataProvider\Product\Listing\Collector;

use MageCloud\Discountpercentage\Api\Data\ProductRender\SavedPriceInfoInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class SavedPrice
 */
class SavedPrice implements ProductRenderCollectorInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var SavedPriceInfoInterfaceFactory
     */
    private $savedPriceInfoFactory;

    /**
     * @var PriceInfoExtensionFactory
     */
    private $priceInfoExtensionFactory;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * SavedPrice constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param SavedPriceInfoInterfaceFactory $savedPriceInfoFactory
     * @param PriceInfoExtensionFactory $priceInfoExtensionFactory
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        SavedPriceInfoInterfaceFactory $savedPriceInfoFactory,
        PriceInfoExtensionFactory $priceInfoExtensionFactory,
        CalculatorInterface $calculator
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->savedPriceInfoFactory = $savedPriceInfoFactory;
        $this->priceInfoExtensionFactory = $priceInfoExtensionFactory;
        $this->calculator = $calculator;
    }

    /**
     * @inheritDoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $savedPrice = $this->savedPriceInfoFactory->create();

        /** @var \Magento\Framework\Pricing\Price\PriceInterface $priceModel */
        $priceModel = $product->getPriceInfo()->getPrice('regular_price');

        /** @var \Magento\Framework\Pricing\Price\PriceInterface $finalPriceModel */
        $finalPriceModel = $product->getPriceInfo()->getPrice('final_price');

        $price = $priceModel->getValue() - $finalPriceModel->getValue();

        $savedPrice->setSavedPrice($this->priceCurrency->format(
            $this->calculator->getAmount($price, $product)->getValue(),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $productRender->getStoreId(),
            $productRender->getCurrencyCode()
        ));
        $savedPrice->setIsSavedPrice($priceModel->getValue() < $finalPriceModel->getValue());

        $priceInfo = $productRender->getPriceInfo();
        $extensionAttributes = $priceInfo->getExtensionAttributes();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->priceInfoExtensionFactory->create();
        }

        $extensionAttributes->setSaved($savedPrice);
    }
}
