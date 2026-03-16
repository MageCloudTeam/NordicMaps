<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Status extends Select
{
    /**
     * @var string[]
     */
    private $stateStatuses = [
        Order::STATE_NEW,
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
        Order::STATE_CLOSED,
        Order::STATE_CANCELED,
        Order::STATE_HOLDED,
    ];
    
    /**
     * @var Config
     */
    private $orderConfig;

    /**
     * @param Context $context
     * @param Config $orderConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $orderConfig,
        array $data = []
    ) {
        $this->orderConfig = $orderConfig;
        
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $statuses = $this->stateStatuses
                ? $this->orderConfig->getStateStatuses($this->stateStatuses)
                : $this->orderConfig->getStatuses();
            
            $this->setOptions($statuses);
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
