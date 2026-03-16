<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Customer;

use Klarna\Kp\Model\Configuration\Payment;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class TypeResolver
{
    /**
     * @var Payment
     */
    private Payment $paymentConfig;

    /**
     * @param Payment $paymentConfig
     * @codeCoverageIgnore
     */
    public function __construct(Payment $paymentConfig)
    {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Getting back customer data
     *
     * @param CartInterface $quote
     * @return string
     */
    public function getData(CartInterface $quote): string
    {
        $store = $quote->getStore();

        $customerType = 'person';
        if ($this->paymentConfig->isB2bEnabled($store) &&
            $this->isAnOrganizationPlacingOrder($quote)) {
            $customerType = 'organization';
        }

        return $customerType;
    }

    /**
     * Check to see if the customer filled the company name in the billing/shipping address or not
     *
     * @param CartInterface $quote
     * @return bool
     */
    private function isAnOrganizationPlacingOrder(CartInterface $quote): bool
    {
        return $quote->getBillingAddress() && $quote->getBillingAddress()->getCompany();
    }
}
