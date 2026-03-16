<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Shipping;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Base\Helper\KlarnaConfig;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * @internal
 */
class Processor
{
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;
    /**
     * @var PrePurchaseCalculator
     */
    private PrePurchaseCalculator $prePurchaseCalculator;
    /**
     * @var PostPurchaseCalculator
     */
    private PostPurchaseCalculator $postPurchaseCalculator;

    /**
     * @param KlarnaConfig $klarnaConfig
     * @param PrePurchaseCalculator $prePurchaseCalculator
     * @param PostPurchaseCalculator $postPurchaseCalculator
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaConfig $klarnaConfig,
        PrePurchaseCalculator $prePurchaseCalculator,
        PostPurchaseCalculator $postPurchaseCalculator
    ) {
        $this->klarnaConfig = $klarnaConfig;
        $this->prePurchaseCalculator = $prePurchaseCalculator;
        $this->postPurchaseCalculator = $postPurchaseCalculator;
    }

    /**
     * Processing the data and putting the data into the Parameter instance for the pre purchase
     *
     * @param DataHolder $dataHolder
     * @param Parameter $parameter
     */
    public function processPrePurchase(DataHolder $dataHolder, Parameter $parameter): void
    {
        if (!$this->klarnaConfig->isSeparateTaxLine($dataHolder->getStore())) {
            $this->prePurchaseCalculator->calculateIncludedTaxData($dataHolder);
        } else {
            $this->prePurchaseCalculator->calculateSeparateTaxLineData($dataHolder);
        }

        $parameter->setShippingUnitPrice($this->prePurchaseCalculator->getUnitPrice());
        $parameter->setShippingTaxRate($this->prePurchaseCalculator->getTaxRate());
        $parameter->setShippingTotalAmount($this->prePurchaseCalculator->getTotalAmount());
        $parameter->setShippingTaxAmount($this->prePurchaseCalculator->getTaxAmount());
        $parameter->setShippingDiscountAmount($this->prePurchaseCalculator->getDiscountAmount());
        $parameter->setShippingTitle($this->prePurchaseCalculator->getTitle());
        $parameter->setShippingReference($this->prePurchaseCalculator->getReference());
    }

    /**
     * Processing the data and putting the data into the Parameter instance for the post purchase
     *
     * @param DataHolder $dataHolder
     * @param Parameter $parameter
     * @param OrderInterface $order
     */
    public function processPostPurchase(DataHolder $dataHolder, Parameter $parameter, OrderInterface $order): void
    {
        $this->postPurchaseCalculator->calculate($dataHolder, $order);

        $parameter->setShippingUnitPrice($this->postPurchaseCalculator->getUnitPrice());
        $parameter->setShippingTaxRate($this->postPurchaseCalculator->getTaxRate());
        $parameter->setShippingTotalAmount($this->postPurchaseCalculator->getTotalAmount());
        $parameter->setShippingTaxAmount($this->postPurchaseCalculator->getTaxAmount());
        $parameter->setShippingDiscountAmount($this->postPurchaseCalculator->getDiscountAmount());
        $parameter->setShippingTitle($this->postPurchaseCalculator->getTitle());
        $parameter->setShippingReference($this->postPurchaseCalculator->getReference());
    }
}
