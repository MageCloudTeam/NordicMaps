<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class CaptureMode
 */
class CaptureMode extends Select
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $options = [
            [
                'value' => Invoice::CAPTURE_ONLINE,
                'label' => __('Capture Online'),
            ],
            [
                'value' => Invoice::CAPTURE_OFFLINE,
                'label' => __('Capture Offline'),
            ],
            [
                'value' => Invoice::NOT_CAPTURE,
                'label' => __('Not Capture'),
            ],
        ];

        $this->setOptions($options);

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     *
     * @return \MageCloud\AutoInvoice\Block\Adminhtml\Form\Field\CaptureMode
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
