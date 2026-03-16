<?php
declare(strict_types=1);

namespace MageCloud\OrderEmailCopy\Block\Adminhtml\Form\Field;

use MageCloud\OrderEmailCopy\Block\Adminhtml\Menu\Config\Backend\Options;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class OrderEmailCopyRule extends AbstractFieldArray
{

    /**
     * @var Options
     */
    protected $countryRenderer = null;

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'brand_code',
            [
                'label' => __('Brand Code'),
                'renderer' => $this->getCountryRenderer()
            ]
        );
        $this->addColumn(
            'brand_email',
            [
                'label'     => __('Brand Email')
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                Options::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $brands_code = $row->getData('brand_code');
        $options = [];
        if ($brands_code) {
            $brands_code = explode(',', $brands_code);
            foreach ($brands_code as $brand_code) {
                $options['option_' . $this->getCountryRenderer()->calcOptionHash($brand_code)]
                    = 'selected="selected"';
            }
        }
        $row->setData('option_extra_attrs', $options);
    }
}
