<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Calculator;

use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Tax\Model\Calculation;

/**
 * @api
 */
class Shipping
{
    /** @var Calculation $calculator */
    private $calculator;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /**
     * @param Calculation          $calculator
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        Calculation $calculator,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->calculator   = $calculator;
        $this->scopeConfig  = $scopeConfig;
    }

    /**
     * Getting back the tax rate
     *
     * @param DataHolder $dataHolder
     * @return float
     */
    public function getTaxRate(DataHolder $dataHolder): float
    {
        $shippingAddress = $dataHolder->getShippingAddress();
        $shippingAmount = (float) $shippingAddress->getShippingAmount();
        if (empty($shippingAmount)) {
            return 0;
        }

        return ($shippingAddress->getBaseShippingInclTax() / $shippingAddress->getShippingAmount() - 1) * 100;
    }
}
