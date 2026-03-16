<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin;

use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Items\Shipping\Handler;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class ShippingLinePlugin
{
    /**
     * @var KssConfigProvider
     */
    private $config;

    /**
     * @param KssConfigProvider $config
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config)
    {
        $this->config = $config;
    }

    /**
     * Disable the shipping line for pre-purchase requests when KSS is enabled
     *
     * @param Handler $subject
     * @param Parameter $parameter
     * @param DataHolder $dataHolder
     * @param CartInterface $quote
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function beforeCollectPrePurchase(
        Handler $subject,
        Parameter $parameter,
        DataHolder $dataHolder,
        CartInterface $quote
    ): array {
        if ($this->config->isKssEnabled($quote->getStore())) {
            $parameter->setShippingLineEnabled(false);
            $totals = $dataHolder->getTotals();
            unset($totals['shipping']);
            $dataHolder->setTotals($totals);
        }
        return [$parameter, $dataHolder, $quote];
    }
}
