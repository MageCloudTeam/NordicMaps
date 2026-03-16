<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Block\Adminhtml\System\Config\Checkout;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * @internal
 */
class Customcheckboxes extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->_template = 'Klarna_Kco::system/config/form/field/array.phtml';
        parent::__construct($context, $data);
    }

    /**
     * Prepare Render Columns
     *
     * @return void
     */
    public function _prepareToRender()
    {
        $this->addColumn(
            'id',
            [
                'label' => __('Checkbox Id'),
                'style' => 'width:100px',
            ]
        );

        $this->addColumn(
            'checked',
            [
                'label' => __('Checked By Default')
            ]
        );

        $this->addColumn(
            'required',
            [
                'label' => __('Required By Default')
            ]
        );

        $this->addColumn(
            'text',
            [
                'label' => __('Checkbox Text'),
                'style' => 'width:300px',
            ]
        );
    }

    /**
     * Render Cell Template
     *
     * @param string $columnName
     * @return mixed|string
     */
    public function renderCellTemplate($columnName)
    {
        if (($columnName === 'checked' || $columnName === 'required') && isset($this->_columns[$columnName])) {
            $options = [1 => 'Yes', 0 => 'No'];
            $element = $this->elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }
}
