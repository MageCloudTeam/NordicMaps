<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Configuration;

use Klarna\Base\Model\Configuration\AbstractConfiguration;
use Klarna\Kco\Model\Payment\Kco;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class Checkout extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = Kco::METHOD_CODE;

    /**
     * Getting back the payment method list
     *
     * @param StoreInterface $store
     * @return array
     */
    public function getExternalPaymentMethodList(StoreInterface $store): array
    {
        $result = $this->getPaymentContentValue($store, 'external_payment_methods');
        return $this->generateArrayResult($result);
    }

    /**
     * Getting back allowed shipping countries
     *
     * @param StoreInterface $store
     * @return array
     */
    public function getShippingCountries(StoreInterface $store): array
    {
        $result = $this->getPaymentContentValue($store, 'shipping_countries');
        return $this->generateArrayResult($result);
    }

    /**
     * Getting back allowed billing countries
     *
     * @param StoreInterface $store
     * @return array
     */
    public function getBillingCountries(StoreInterface $store): array
    {
        $result = $this->getPaymentContentValue($store, 'billing_countries');
        return $this->generateArrayResult($result);
    }

    /**
     * Returns true if the billing and shipping country must be the same
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isSameBillingShippingCountry(StoreInterface $store): bool
    {
        return $this->getPaymentFlagValue($store, 'shipping_and_billing_countries_same');
    }

    /**
     * Getting back the design
     *
     * @param StoreInterface $store
     * @return array
     */
    public function getDesign(StoreInterface $store): array
    {
        $result = $this->scopeConfig->getValue(
            'checkout/' . Kco::METHOD_CODE . '_design',
            ScopeInterface::SCOPE_STORES,
            $store
        );

        if (empty($result)) {
            return [];
        }

        return array_map('trim', array_filter($result));
    }

    /**
     * Returns true if KP is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEnabled(StoreInterface $store): bool
    {
        return $this->scopeConfig->isSetFlag(
            sprintf('payment/klarna_kp/%s', 'active'),
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * Returns true if B2B is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isB2bEnabled(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'enable_b2b');
    }

    /**
     * Returns true if auto focus is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isAutoFocusEnabled(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'auto_focus');
    }

    /**
     * Getting back the configured order status for new orders
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getOrderStatusForNewOrders(StoreInterface $store): string
    {
        return $this->getPaymentContentValue($store, 'order_status');
    }

    /**
     * Getting back the business ID attribute
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getBusinessIdAttribute(StoreInterface $store): string
    {
        return $this->getCheckoutContentValue($store, 'business_id_attribute');
    }
}
