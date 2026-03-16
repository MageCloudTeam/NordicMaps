<?php

namespace MageCloud\OrderEmailCopy\Block\Adminhtml\Menu\Config\Backend;

class Options extends \Magento\Framework\View\Element\Html\Select
{
    protected $eavConfig;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\View\Element\Context        $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        array                                          $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_eavConfig = $eavConfig;
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $attributeCode = "map_series";
        $attribute = $this->_eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        $brands = [];
        foreach ($options as $option) {
            if ($option['value'] > 0) {
                $brands[] = $option;
            }
        }
        $this->setOptions($brands);

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value . '[]');
    }
}
