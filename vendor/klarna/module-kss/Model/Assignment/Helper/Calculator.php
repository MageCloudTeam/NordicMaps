<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Assignment\Helper;

use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @api
 */
class Calculator
{
    /**
     * Getting back the shipping amount exclusive taxes.
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @return float
     */
    public function getShippingAmountExclusiveTax(ShippingMethodGatewayInterface $shippingMethodGateway): float
    {
        return $shippingMethodGateway->getShippingAmount() - $shippingMethodGateway->getTaxAmount();
    }

    /**
     * Getting back the discount tax compensation amount
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @param float                          $subtractedDiscountShippingTax
     * @return float
     */
    public function getDiscountTaxCompensationAmount(
        ShippingMethodGatewayInterface $shippingMethodGateway,
        float $subtractedDiscountShippingTax
    ): float {
        return $shippingMethodGateway->getTaxAmount() - $subtractedDiscountShippingTax;
    }

    /**
     * Getting back the shipping tax value after the discount was applied on it
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @param CartInterface                  $quote
     * @return float
     */
    public function getSubtractedDiscountShippingTax(
        ShippingMethodGatewayInterface $shippingMethodGateway,
        CartInterface $quote
    ): float {
        $taxRate = 1 + ($shippingMethodGateway->getTaxRate() / 100);
        $shippingAmount = $shippingMethodGateway->getShippingAmount() -
            $quote->getShippingAddress()->getShippingDiscountAmount();
        $amountWithoutTax = $shippingAmount / $taxRate;

        return $shippingAmount - $amountWithoutTax;
    }
}
