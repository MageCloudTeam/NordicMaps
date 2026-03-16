<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Class EmailCopyOfInvoice
 */
class EmailCopyOfInvoice extends Select
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
                'value' => 0,
                'label' => __('Disabled'),
            ],
            [
                'value' => 1,
                'label' => __('Enabled'),
            ]
        ];

        $this->setOptions($options);

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     *
     * @return \MageCloud\AutoInvoice\Block\Adminhtml\Form\Field\EmailCopyOfInvoice
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}