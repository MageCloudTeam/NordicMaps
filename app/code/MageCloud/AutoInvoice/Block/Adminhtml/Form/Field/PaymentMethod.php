<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Block\Adminhtml\Form\Field;

use MageCloud\AutoInvoice\Model\Config;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Payment\Model\Method\Factory;
use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class PaymentMethod extends Select
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Factory
     */
    private $paymentMethodFactory;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var
     */
    private $websiteId;

    /**
     * @var
     */
    private $storeId;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var int|mixed|null
     */
    private $scopeId;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Factory $paymentMethodFactory
     * @param Http $request
     * @param array $data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Factory $paymentMethodFactory,
        Http $request,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->request = $request;

        parent::__construct($context, $data);

        // Find store ID and scope
        $this->websiteId = $request->getParam('website', 0);
        $this->storeId   = $request->getParam('store', 0);
        $this->scope     = $request->getParam('scope');

        // Website scope
        if ($this->websiteId) {
            $this->scope = !$this->scope ? 'website' : $this->scope;
        } else {
            $this->websiteId = $storeManager->getWebsite()->getId();
        }

        // Store scope
        if ($this->storeId) {
            $this->websiteId = $storeManager->getStore($this->storeId)->getWebsite()->getId();
            $this->scope = !$this->scope ? 'store' : $this->scope;
        } else {
            $this->storeId = $storeManager->getWebsite($this->websiteId)->getDefaultStore()->getId();
        }

        // Set scope ID
        switch ($this->scope) {
            case 'website':
                $this->scopeId = $this->websiteId;
                break;
            case 'store':
                $this->scopeId = $this->storeId;
                break;
            default:
                $this->scope = 'default';
                $this->scopeId = 0;
                break;
        }
    }

    private function getActiveMethods()
    {
        $methods = [];

        foreach ($this->_scopeConfig->getValue('payment', $this->scope, $this->scopeId) as $code => $data) {
            if (isset($data['active'], $data['model']) && (bool)$data['active']) {
                /** @var MethodInterface $methodModel Actually it's wrong interface */
                $methodModel = $this->paymentMethodFactory->create($data['model']);
                $methodModel->setStore($this->storeId);
                if ($methodModel->getConfigData('active', $this->storeId)) {
                    $methods[$code] = $methodModel;
                }
            }
        }

        return $methods;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = [
                [
                    'value' => Config::RULE_PAYMENT_METHOD_ALL,
                    'label' => __('Any'),
                ],
            ];

            $paymentMethods = $this->getActiveMethods();
            foreach ($paymentMethods as $code => $model) {
                $options[] = [
                    'value' => $code,
                    'label' => $model->getTitle() ? $model->getTitle() . ' (' . $code . ')' : $code,
                ];
            }

            $this->setOptions($options);
        }

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     *
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
