<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model;

use Klarna\Orderlines\Model\Container\Parameter;

/**
 * @internal
 */
class ItemGenerator
{

    public const ITEM_TYPE_DISCOUNT = 'discount';
    public const ITEM_TYPE_CUSTOMERBALANCE = 'store_credit';
    public const ITEM_TYPE_GIFTCARD = 'gift_card';
    public const ITEM_TYPE_REWARD = 'discount';
    public const ITEM_TYPE_SHIPPING = 'shipping_fee';
    public const ITEM_TYPE_SURCHARGE = 'surcharge';
    public const ITEM_TYPE_TAX = 'sales_tax';

    /**
     * Getting back the discount item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getDiscountItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_DISCOUNT,
            $parameter->getDiscountReference(),
            $parameter->getDiscountTitle(),
            $quantity,
            $parameter->getDiscountUnitPrice(),
            $parameter->getDiscountTaxRate(),
            $parameter->getDiscountTotalAmount(),
            $parameter->getDiscountTaxAmount()
        );
    }

    /**
     * Getting back the customer balance item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getCustomerBalanceItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_CUSTOMERBALANCE,
            $parameter->getCustomerbalanceReference(),
            $parameter->getCustomerbalanceTitle(),
            $quantity,
            $parameter->getCustomerbalanceUnitPrice(),
            $parameter->getCustomerbalanceTaxRate(),
            $parameter->getCustomerbalanceTotalAmount(),
            $parameter->getCustomerbalanceTaxAmount()
        );
    }

    /**
     * Getting back the gift cart item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getGiftCartItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_GIFTCARD,
            $parameter->getGiftCardAccountReference(),
            $parameter->getGiftCardAccountTitle(),
            $quantity,
            $parameter->getGiftCardAccountUnitPrice(),
            $parameter->getGiftCardAccountTaxRate(),
            $parameter->getGiftCardAccountTotalAmount(),
            $parameter->getGiftCardAccountTaxAmount()
        );
    }

    /**
     * Getting back the rewad item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getRewardItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_REWARD,
            $parameter->getRewardReference(),
            $parameter->getRewardTitle(),
            $quantity,
            $parameter->getRewardUnitPrice(),
            $parameter->getRewardTaxRate(),
            $parameter->getRewardTotalAmount(),
            $parameter->getRewardTaxAmount()
        );
    }

    /**
     * Getting back the shipping item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getShippingItemRow(Parameter $parameter, int $quantity): array
    {
        $result = $this->getItem(
            self::ITEM_TYPE_SHIPPING,
            $parameter->getShippingReference(),
            $parameter->getShippingTitle(),
            $quantity,
            $parameter->getShippingUnitPrice(),
            $parameter->getShippingTaxRate(),
            $parameter->getShippingTotalAmount(),
            $parameter->getShippingTaxAmount()
        );

        $result['total_discount_amount'] = $parameter->getShippingDiscountAmount();
        return $result;
    }

    /**
     * Getting back the surcharge item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getSurchargeItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_SURCHARGE,
            $parameter->getSurchargeReference(),
            $parameter->getSurchargeName(),
            $quantity,
            $parameter->getSurchargeUnitPrice(),
            0,
            $parameter->getSurchargeTotalAmount(),
            0
        );
    }

    /**
     * Getting back the tax item row
     *
     * @param Parameter $parameter
     * @param int $quantity
     * @return array
     */
    public function getTaxItemRow(Parameter $parameter, int $quantity): array
    {
        return $this->getItem(
            self::ITEM_TYPE_TAX,
            __('Sales Tax')->getText(),
            __('Sales Tax')->getText(),
            $quantity,
            $parameter->getTaxUnitPrice(),
            0,
            $parameter->getTaxTotalAmount(),
            0
        );
    }

    /**
     * Getting back them item
     *
     * @param string $type
     * @param string $reference
     * @param string $name
     * @param int $quantity
     * @param float $unitPrice
     * @param float $taxRate
     * @param float $totalAmount
     * @param float $totalTaxAmount
     * @return array
     */
    private function getItem(
        string $type,
        string $reference,
        string $name,
        int $quantity,
        float $unitPrice,
        float $taxRate,
        float $totalAmount,
        float $totalTaxAmount
    ): array {
        return [
            'type' => $type,
            'reference' => $reference,
            'name' => $name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'total_amount' => $totalAmount,
            'total_tax_amount' => $totalTaxAmount
        ];
    }
}
