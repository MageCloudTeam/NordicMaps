<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Customer;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Configuration\Payment;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * @internal
 */
class Generator
{

    /**
     * @var TypeResolver
     */
    private TypeResolver $typeResolver;
    /**
     * @var DateTime
     */
    private DateTime $dateTime;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var Payment
     */
    private Payment $paymentConfig;

    /**
     * Generate customer data
     *
     * @param TypeResolver $typeResolver
     * @param DateTime $dateTime
     * @param ApiValidation $apiValidation
     * @param Payment $paymentConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        TypeResolver $typeResolver,
        DateTime $dateTime,
        ApiValidation $apiValidation,
        Payment $paymentConfig
    ) {
        $this->typeResolver = $typeResolver;
        $this->dateTime = $dateTime;
        $this->apiValidation = $apiValidation;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Getting back the basic data
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getBasicData(CartInterface $quote): array
    {
        return [
            'type' => $this->typeResolver->getData($quote)
        ];
    }

    /**
     * Getting customer data with prefilled data
     *
     * @param CartInterface $magentoQuote
     * @return array
     */
    public function getWithPrefilledData(CartInterface $magentoQuote): array
    {
        $customer = $this->getBasicData($magentoQuote);

        if (!$magentoQuote->getCustomerIsGuest() && $magentoQuote->getCustomerDob()) {
            $customer['date_of_birth'] = $this->dateTime->date('Y-m-d', $magentoQuote->getCustomerDob());
        }

        return $customer;
    }

    /**
     * Returns true if prefill is allowed
     *
     * @param CartInterface $magentoQuote
     * @return bool
     */
    public function isPrefillAllowed(CartInterface $magentoQuote): bool
    {
        $store = $magentoQuote->getStore();

        if (!$this->paymentConfig->isDataSharingEnabled($store)) {
            return false;
        }
        if (!$this->apiValidation->isKpEndpointSelectedForUsMarket($store)) {
            return false;
        }

        $billingAddress = $magentoQuote->getBillingAddress();
        return $billingAddress->getCountryId() === 'US';
    }
}
