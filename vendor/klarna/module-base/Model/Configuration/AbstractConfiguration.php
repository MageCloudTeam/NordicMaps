<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Configuration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
abstract class AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = '';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Getting back the payment flag value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function getPaymentFlagValue(StoreInterface $store, string $key): bool
    {
        return $this->getFlagValue($store, $key, $this->paymentCode, 'payment');
    }

    /**
     * Getting back the checkout flag value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function getCheckoutFlagValue(StoreInterface $store, string $key): bool
    {
        return $this->getFlagValue($store, $key, $this->paymentCode, 'checkout');
    }

    /**
     * Getting back the checkout flag value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function getKlarnaFlagValue(StoreInterface $store, string $key): bool
    {
        return $this->getFlagValue($store, $key, $this->paymentCode, 'klarna');
    }

    /**
     * Getting back the flag value
     *
     * @param StoreInterface $store
     * @param string $key
     * @param string $paymentCode
     * @param string $scope
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    private function getFlagValue(StoreInterface $store, string $key, string $paymentCode, string $scope)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            sprintf($scope . '/' . $paymentCode . '/%s', $key),
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * Getting back the payment content value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return string
     */
    protected function getPaymentContentValue(StoreInterface $store, string $key): string
    {
        return $this->getContentValue($store, $key, $this->paymentCode, 'payment');
    }

    /**
     * Getting back the checkout content value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return string
     */
    protected function getCheckoutContentValue(StoreInterface $store, string $key): string
    {
        return $this->getContentValue($store, $key, $this->paymentCode, 'checkout');
    }

    /**
     * Getting back the checkout content value
     *
     * @param StoreInterface $store
     * @param string $key
     * @return string
     */
    protected function getKlarnaContentValue(StoreInterface $store, string $key): string
    {
        return $this->getContentValue($store, $key, $this->paymentCode, 'klarna');
    }

    /**
     * Getting back the content value
     *
     * @param StoreInterface $store
     * @param string $key
     * @param string $paymentCode
     * @param string $scope
     * @return string
     */
    private function getContentValue(StoreInterface $store, string $key, string $paymentCode, string $scope): string
    {
        return (string) $this->scopeConfig->getValue(
            sprintf($scope . '/' . $paymentCode . '/%s', $key),
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * Generating a array result based on the string input
     *
     * @param string $input
     * @return array
     */
    protected function generateArrayResult(string $input): array
    {
        if ($input === '') {
            return [];
        }

        return explode(',', $input);
    }
}
