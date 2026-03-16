<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ProcessingRule extends AbstractFieldArray
{
    /**
     * @var Status
     */
    private $destinationStatusRenderer = null;

    /**
     * @var PaymentMethod
     */
    private $paymentMethodRenderer = null;

    /**
     * @var CaptureMode
     */
    private $captureModeRenderer = null;

    /**
     * @var EmailCopyOfInvoice
     */
    private $emailCopyOfInvoiceRender = null;

    /**
     * Returns renderer for destination status element
     */
    protected function getDestinationStatusRenderer()
    {
        if (!$this->destinationStatusRenderer) {
            $this->destinationStatusRenderer = $this->getLayout()->createBlock(
                Status::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        
        return $this->destinationStatusRenderer;
    }

    /**
     * Returns renderer for payment method
     */
    protected function getPaymentMethodRenderer()
    {
        if (!$this->paymentMethodRenderer) {
            $this->paymentMethodRenderer = $this->getLayout()->createBlock(
                PaymentMethod::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->paymentMethodRenderer;
    }

    /**
     * Returns renderer for capture mode
     */
    protected function getCaptureModeRenderer()
    {
        if (!$this->captureModeRenderer) {
            $this->captureModeRenderer = $this->getLayout()->createBlock(
                CaptureMode::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->captureModeRenderer;
    }

    /**
     * Returns renderer for capture mode
     */
    protected function getEmailCopyOfInvoiceRenderer()
    {
        if (!$this->emailCopyOfInvoiceRender) {
            $this->emailCopyOfInvoiceRender = $this->getLayout()->createBlock(
                EmailCopyOfInvoice::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->emailCopyOfInvoiceRender;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'payment_method',
            [
                'label' => __('Payment Method'),
                'renderer'  => $this->getPaymentMethodRenderer(),
            ]
        );
        $this->addColumn(
            'destination_status',
            [
                'label'     => __('Destination Status'),
                'renderer'  => $this->getDestinationStatusRenderer(),
            ]
        );
        $this->addColumn(
            'capture_mode',
            [
                'label'     => __('Amount'),
                'renderer'  => $this->getCaptureModeRenderer(),
            ]
        );
        $this->addColumn(
            'email_copy_of_invoice',
            [
                'label'     => __('Email Copy of Invoice'),
                'renderer'  => $this->getEmailCopyOfInvoiceRenderer(),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $destinationStatus = $row->getDestinationStatus();
        $paymentMethod = $row->getPaymentMethod();
        $captureMode = $row->getCaptureMode();
        $emailCopyOfInvoice = $row->getEmailCopyOfInvoice();

        $options = [];
        if ($destinationStatus) {
            $options['option_' . $this->getDestinationStatusRenderer()->calcOptionHash($destinationStatus)]
                = 'selected="selected"';
            
            $options['option_' . $this->getPaymentMethodRenderer()->calcOptionHash($paymentMethod)]
                = 'selected="selected"';
            
            $options['option_' . $this->getCaptureModeRenderer()->calcOptionHash($captureMode)]
                = 'selected="selected"';

            $options['option_' . $this->getEmailCopyOfInvoiceRenderer()->calcOptionHash($emailCopyOfInvoice)]
                = 'selected="selected"';
        }
        
        $row->setData('option_extra_attrs', $options);
    }
}
