<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Cart\Validations;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Helper\DataConverter;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Orderlines\Model\ItemGenerator;
use Klarna\Kco\Api\CheckoutValidationInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Tax\Model\Config;

/**
 * Validating the shipping amount between the Klarna order and the shop quote
 *
 * @internal
 */
class ShippingAmount implements CheckoutValidationInterface
{
    /**
     * @var DataConverter
     */
    private DataConverter $dataConverter;
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;
    /**
     * @var Config
     */
    private Config $taxConfig;

    /**
     * @param DataConverter $dataConverter
     * @param KlarnaConfig  $klarnaConfig
     * @param Config        $taxConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        DataConverter $dataConverter,
        KlarnaConfig  $klarnaConfig,
        Config        $taxConfig
    ) {
        $this->dataConverter = $dataConverter;
        $this->klarnaConfig  = $klarnaConfig;
        $this->taxConfig     = $taxConfig;
    }

    /**
     * @inheritDoc
     */
    public function validate(DataObject $request, CartInterface $quote): void
    {
        if ($quote->isVirtual()) {
            return;
        }

        /** @var AddressInterface $address */
        $address              = $quote->getShippingAddress();
        $quoteShippingAmount  = (int) $this->dataConverter->toApiFloat($this->getQuoteShippingAmount($quote, $address));
        $klarnaShippingAmount = $this->getShippingAmount($request, $quote);
        if ($quoteShippingAmount !== $klarnaShippingAmount) {
            $exceptionMessage = __(
                'Shipping amount does not match for order %1. Klarna amount is %2 vs Magento amount is %3',
                $quote->getReservedOrderId(),
                $klarnaShippingAmount,
                $quoteShippingAmount
            );
            throw new KlarnaException($exceptionMessage);
        }
    }

    /**
     * Getting back the quote shipping amount
     *
     * @param CartInterface $quote
     * @param AddressInterface $address
     * @return float
     * @throws KlarnaException
     */
    private function getQuoteShippingAmount(CartInterface $quote, AddressInterface $address): float
    {
        $shipping = $address->getBaseShippingAmount();
        $discount = $address->getBaseShippingDiscountAmount();
        if (!$this->klarnaConfig->isSeparateTaxLine($quote->getStore())) {
            $shipping = $address->getBaseShippingInclTax();
            if (!$this->taxConfig->shippingPriceIncludesTax($quote->getStore())) {
                $shipping = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount();
            }
        }
        return $shipping - $discount;
    }

    /**
     * Getting back the shipping amount. Its a public method so that it can get extended.
     *
     * @param DataObject    $request
     * @param CartInterface $quote
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getShippingAmount(DataObject $request, CartInterface $quote): int
    {
        foreach ($request->getOrderLines() as $item) {
            if ($item['type'] === ItemGenerator::ITEM_TYPE_SHIPPING) {
                return $item['total_amount'];
            }
        }

        if (isset($request->getSelectedShippingOption()['id'])) {
            return $request->getSelectedShippingOption()['price'];
        }

        return 0;
    }
}
