<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Model\Adminhtml\System\Config;

use MageCloud\AutoInvoice\Model\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class ProcessingRule
 */
class ProcessingRule extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        Json $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare data before save
     *
     * @return \MageCloud\AutoInvoice\Model\Adminhtml\System\Config\ProcessingRule
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $result = [];
        foreach ($value as $data) {
            if (
                empty($data[Config::RULE_PAYMENT_METHOD]) ||
                empty($data[Config::RULE_DESTINATION_STATUS])
            ) {
                continue;
            }

            $result[$data[Config::RULE_PAYMENT_METHOD]] = [
                Config::RULE_DESTINATION_STATUS    => $data[Config::RULE_DESTINATION_STATUS],
                Config::RULE_CAPTURE_MODE          => $data[Config::RULE_CAPTURE_MODE],
                Config::RULE_EMAIL_COPY_OF_INVOICE => $data[Config::RULE_EMAIL_COPY_OF_INVOICE],
            ];
        }

        $this->setValue($this->serializer->serialize($result));

        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());

            if (is_array($value)) {
                $this->setValue($this->encodeArrayFieldValue($value));
            }
        }

        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     *
     * @return array
     */
    private function encodeArrayFieldValue(array $value)
    {
        $result = [];

        foreach ($value as $key => $item) {
            $id = $this->mathRandom->getUniqueHash('_');

            if (is_array($item)) {
                $destinationStatus = $item[Config::RULE_DESTINATION_STATUS];
                $captureMode = $item[Config::RULE_CAPTURE_MODE];
                $emailCopyOfInvoice = $item[Config::RULE_EMAIL_COPY_OF_INVOICE];
            } else {
                $destinationStatus = $item;
                $captureMode = Invoice::CAPTURE_OFFLINE;
                $emailCopyOfInvoice = 0;
            }

            $result[$id] = [
                Config::RULE_PAYMENT_METHOD        => $key,
                Config::RULE_DESTINATION_STATUS    => $destinationStatus,
                Config::RULE_CAPTURE_MODE          => $captureMode,
                Config::RULE_EMAIL_COPY_OF_INVOICE => $emailCopyOfInvoice,
            ];
        }

        return $result;
    }
}