<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**
     * Configuration paths
     */
    const XML_PATH_EXTENSION_ENABLED = 'auto_invoice/general/module_enabled';
    const XML_PATH_PROCESSING_RULES = 'auto_invoice/general/processing_rules';
    const XML_PATH_MODULE_DEBUGGING = 'auto_invoice/general/module_debugging';

    /**
     * Statuses rule
     */
    const RULE_DESTINATION_STATUS = 'destination_status';
    const RULE_PAYMENT_METHOD = 'payment_method';
    const RULE_CAPTURE_MODE = 'capture_mode';
    const RULE_EMAIL_COPY_OF_INVOICE = 'email_copy_of_invoice';
    const RULE_PAYMENT_METHOD_ALL = '*';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $config
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $config,
        SerializerInterface $serializer
    ) {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     *
     * @return bool
     */
    public function isEnabled(
        $scopeCode = null,
        $scope = ScopeInterface::SCOPE_STORES
    ): bool {
        return $this->config->isSetFlag(
            self::XML_PATH_EXTENSION_ENABLED,
            $scope,
            $scopeCode
        );
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     *
     * @return bool
     */
    public function isDebug(
        $scopeCode = null,
        $scope = ScopeInterface::SCOPE_STORES
    ): bool {
        return $this->config->isSetFlag(self::XML_PATH_MODULE_DEBUGGING, $scope, $scopeCode);
    }

    /**
     * Return processing rules
     *
     * @param string $scope
     * @param null $scopeCode
     *
     * @return string|null
     */
    public function getProcessingRules(
        $scopeCode = null,
        $scope = ScopeInterface::SCOPE_STORES
    ): ?string {
        return $this->config->getValue(self::XML_PATH_PROCESSING_RULES, $scope, $scopeCode);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     *
     * @return array
     */
    public function getParsedProcessingRules(
        $scopeCode = null,
        $scope = ScopeInterface::SCOPE_STORES
    ): array {
        $processingRules = $this->getProcessingRules($scopeCode, $scope);

        $rules = [];

        if ($processingRules === null) {
            return $rules;
        }

        foreach ($this->serializer->unserialize($processingRules) as $key => $item) {

            if (is_array($item)) {
                $destinationStatus = $item[self::RULE_DESTINATION_STATUS];
                $captureMode = $item[self::RULE_CAPTURE_MODE];
                $emailCopyOfInvoice = $item[self::RULE_EMAIL_COPY_OF_INVOICE];
            } else {
                $destinationStatus = $item;
                $captureMode = Invoice::CAPTURE_OFFLINE;
                $emailCopyOfInvoice = 0;
            }

            $rules[] = [
                self::RULE_PAYMENT_METHOD        => $key,
                self::RULE_DESTINATION_STATUS    => $destinationStatus,
                self::RULE_CAPTURE_MODE          => $captureMode,
                self::RULE_EMAIL_COPY_OF_INVOICE => $emailCopyOfInvoice,
            ];
        }

        return $rules;
    }
}