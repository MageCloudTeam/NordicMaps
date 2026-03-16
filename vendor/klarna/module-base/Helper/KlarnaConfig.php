<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper;

use Klarna\Base\Api\VersionInterface;
use Klarna\Base\Api\VersionInterfaceFactory;
use Klarna\Base\Exception as KlarnaException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Config\DataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class KlarnaConfig extends AbstractHelper
{
    /**
     * Configuration cache for api versions
     *
     * @var array
     */
    private $versionConfigCache = [];
    /**
     * @var DataInterface
     */
    private $config;
    /**
     * @var VersionInterfaceFactory
     */
    private $versionFactory;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /** @var LoggerInterface $klarnaLogger */
    private $klarnaLogger;

    /**
     * @param Context                 $context
     * @param DataInterface           $config
     * @param VersionInterfaceFactory $versionFactory
     * @param DataObjectFactory       $dataObjectFactory
     * @param LoggerInterface         $klarnaLogger
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        DataInterface $config,
        VersionInterfaceFactory $versionFactory,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $klarnaLogger
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->versionFactory = $versionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->klarnaLogger = $klarnaLogger;
    }

    /**
     * Get configuration parameters for API version
     *
     * @param StoreInterface $store
     * @return VersionInterface
     * @throws KlarnaException
     */
    public function getVersionConfig(StoreInterface $store = null): VersionInterface
    {
        if ($store === null) {
            return $this->getVersionConfigByFullScope(
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                '0'
            );
        }

        return $this->getVersionConfigByFullScope(ScopeInterface::SCOPE_STORES, $store->getCode());
    }

    /**
     * Getting back the version config by a full given scope
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return VersionInterface
     * @throws KlarnaException
     */
    private function getVersionConfigByFullScope(string $scopeType, string $scopeCode): VersionInterface
    {
        $versionPath = 'klarna/api/api_version';

        $version = $this->scopeConfig->getValue($versionPath, $scopeType, $scopeCode);
        if ($version === null) {
            throw new KlarnaException(__('Invalid Api Version: ' . $version));
        }
        if (!array_key_exists($version, $this->versionConfigCache)) {
            $this->versionConfigCache[$version] = $this->getCheckoutVersionDetails($version);
        }

        return $this->versionConfigCache[$version];
    }

    /**
     * Get api version details
     *
     * @param string $code
     *
     * @return VersionInterface
     * @throws KlarnaException
     */
    private function getCheckoutVersionDetails($code)
    {
        $options = $this->getConfig(sprintf('api_versions/%s', $code));
        if ($options === null) {
            $options = [];
        }
        if (!is_array($options)) {
            $options = [$options];
        }
        if (isset($options['options'])) {
            $options = array_merge($options, $options['options']);
            unset($options['options']);
        }
        $options['code'] = $code;

        if (isset($options['type'])) {
            $apiTypeConfig = $this->getApiTypeConfig($options['type']);
            $apiTypeOptions = $apiTypeConfig['options'];
            $apiTypeOptions['ordermanagement'] = $apiTypeConfig['ordermanagement'];
            $options = array_merge($apiTypeOptions, $options);
        } else {
            $this->klarnaLogger->debug(
                'The api configuration for ' . $code . ' is incomplete because some values are missing.'
            );
        }

        return $this->versionFactory->create(['data' => $options]);
    }

    /**
     * Get Klarna config value for $key
     *
     * @param string $key
     * @return mixed
     * @throws \RuntimeException
     */
    private function getConfig(string $key)
    {
        return $this->config->get($key);
    }

    /**
     * Get api type configuration
     *
     * @param string $code
     *
     * @return DataObject
     * @throws KlarnaException
     */
    public function getApiTypeConfig($code)
    {
        $typeConfig = $this->getConfig(sprintf('api_types/%s', $code));
        if (!$typeConfig) {
            throw new KlarnaException(__('Invalid API version selected!'));
        }

        return $typeConfig;
    }

    /**
     * Get merchant checkbox method configuration details
     *
     * @param string $code
     *
     * @return DataObject
     */
    public function getMerchantCheckboxMethodConfig($code)
    {
        $options = $this->getConfig(sprintf('merchant_checkbox/%s', $code));
        if ($options === null) {
            $options = [];
        }
        if (!is_array($options)) {
            $options = [$options];
        }
        $options['code'] = $code;

        return $this->dataObjectFactory->create(['data' => $options]);
    }

    /**
     * Determine if current store supports the use of the merchant checkbox feature
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isMerchantCheckboxSupport($store = null)
    {
        return $this->getVersionConfig($store)->isMerchantCheckboxSupport();
    }

    /**
     * Determine if current store supports the use of phone mandatory
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isPhoneMandatorySupport($store = null)
    {
        return $this->getVersionConfig($store)->isPhoneMandatorySupport();
    }

    /**
     * Determine if current store supports the use of phone mandatory
     *
     * @param Store $store
     *
     * @return string
     * @throws KlarnaException
     */
    public function getOrderMangagementClass($store = null)
    {
        return $this->getVersionConfig($store)->getOrdermanagement();
    }

    /**
     * Determine if current store supports the use of title mandatory
     *
     * @param Store $store
     *
     * @return bool
     * @throws KlarnaException
     */
    public function isTitleMandatorySupport($store = null)
    {
        return $this->getVersionConfig($store)->isTitleMandatorySupport();
    }

    /**
     * Checking if there is a separate tax line
     *
     * @param Store $store
     * @return bool
     * @throws KlarnaException
     */
    public function isSeparateTaxLine(Store $store)
    {
        return (bool) $this->getVersionConfig($store)->isSeparateTaxLine();
    }

    /**
     * Getting back external payment methods
     *
     * @param string $code
     * @return mixed
     */
    public function getExternalPaymentOptions(string $code)
    {
        return $this->getConfig(sprintf('external_payment_methods/%s', $code));
    }
}
