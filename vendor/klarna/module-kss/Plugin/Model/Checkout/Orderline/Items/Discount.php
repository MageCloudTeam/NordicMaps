<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Model\Checkout\Orderline\Items;

use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Orderlines\Model\Items\Discount\Handler as OrderLineDiscount;

/**
 * @internal
 */
class Discount
{
    /**
     * @var KssConfigProvider
     */
    private $config;

    /**
     * ProcessMerchantUrlsPlugin constructor.
     *
     * @param KssConfigProvider $config
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config)
    {
        $this->config = $config;
    }

    /**
     * Disable the shipping line for pre-purchase requests when KSS is enabled.
     *
     * @param OrderLineDiscount $subject
     * @param Parameter $parameter
     * @param DataHolder $dataHolder
     * @param CartInterface $quote
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function beforeCollectPrePurchase(
        OrderLineDiscount $subject,
        Parameter $parameter,
        DataHolder $dataHolder,
        CartInterface $quote
    ): array {
        if ($this->config->isKssEnabled($quote->getStore())) {
            $parameter->setShippingLineEnabled(false);
        }

        return [$parameter, $dataHolder, $quote];
    }
}
