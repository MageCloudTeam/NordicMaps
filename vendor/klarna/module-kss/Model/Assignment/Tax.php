<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Assignment;

use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Klarna\Kss\Model\Assignment\Helper\Calculator;
use Klarna\Kss\Model\Assignment\Helper\AppliedShippingTaxFactory;
use Klarna\Kss\Model\Carrier;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Tax\Api\Data\TaxDetailsItemInterface;

/**
 * @api
 */
class Tax
{
    /**
     * @var Calculator
     */
    private Calculator $calculator;
    /**
     * @var AppliedShippingTaxFactory
     */
    private AppliedShippingTaxFactory $factory;

    /**
     * @param Calculator                $calculator
     * @param AppliedShippingTaxFactory $factory
     * @codeCoverageIgnore
     */
    public function __construct(Calculator $calculator, AppliedShippingTaxFactory $factory)
    {
        $this->calculator = $calculator;
        $this->factory = $factory;
    }

    /**
     * Returns true when the tax values can be adjusted
     *
     * @param TaxDetailsItemInterface $tax
     * @param CartInterface           $quote
     * @return bool
     */
    public function canUpdateValues(TaxDetailsItemInterface $tax, CartInterface $quote): bool
    {
        if ($tax->getType() !== 'shipping') {
            return false;
        }

        if ($quote->isVirtual()) {
            return false;
        }

        if ($quote->getId() === null) {
            return false;
        }

        return true;
    }

    /**
     * Assigning the shipping information to the given tax instance
     *
     * @param TaxDetailsItemInterface        $tax
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @param CartInterface                  $quote
     * @return TaxDetailsItemInterface
     */
    public function assignToTaxInstance(
        TaxDetailsItemInterface $tax,
        ShippingMethodGatewayInterface $shippingMethodGateway,
        CartInterface $quote
    ): TaxDetailsItemInterface {
        $taxSubtractedDiscount = $this->calculator->getSubtractedDiscountShippingTax($shippingMethodGateway, $quote);
        $priceExclTax = $this->calculator->getShippingAmountExclusiveTax($shippingMethodGateway);

        $tax->setTaxPercent($shippingMethodGateway->getTaxRate());
        $tax->setPrice($priceExclTax);
        $tax->setPriceInclTax($shippingMethodGateway->getShippingAmount());
        $tax->setRowTotal($priceExclTax);
        $tax->setRowTotalInclTax($shippingMethodGateway->getShippingAmount());
        $tax->setRowTax($taxSubtractedDiscount);
        $tax->setDiscountTaxCompensationAmount(
            $this->calculator->getDiscountTaxCompensationAmount($shippingMethodGateway, $taxSubtractedDiscount)
        );

        $tax->setAppliedTaxes(
            [Carrier::GATEWAY_KEY => $this->factory->getAppliedTax($shippingMethodGateway)]
        );
        return $tax;
    }
}
