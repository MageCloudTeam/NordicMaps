<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Configuration;

use Klarna\Base\Model\Configuration\AbstractConfiguration;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class Payment extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = Kp::METHOD_CODE;

    /**
     * Getting back a list of allowed countries or a empty list which indicates that all countries are allowed
     *
     * @param StoreInterface $store
     * @return array
     */
    public function getAllowedCountries(StoreInterface $store): array
    {
        $result = $this->getPaymentContentValue($store, 'specificcountry');
        if ($result === '') {
            return [];
        }

        return explode(',', $result);
    }

    /**
     * Returns true if B2B is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isB2bEnabled(StoreInterface $store): bool
    {
        return $this->getPaymentFlagValue($store, 'enable_b2b');
    }

    /**
     * Returns true if data sharing on load is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isDataSharingOnLoadEnabled(StoreInterface $store): bool
    {
        return $this->getPaymentFlagValue($store, 'data_sharing_onload');
    }

    /**
     * Returns true if data sharing is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isDataSharingEnabled(StoreInterface $store): bool
    {
        return $this->getPaymentFlagValue($store, 'data_sharing');
    }

    /**
     * Returns true if KP is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEnabled(StoreInterface $store): bool
    {
        return $this->getPaymentFlagValue($store, 'active');
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
            sprintf('checkout/' . Kp::METHOD_CODE . '_design'),
            ScopeInterface::SCOPE_STORES,
            $store
        );

        if (empty($result)) {
            return [];
        }

        return array_map('trim', array_filter($result));
    }
}
