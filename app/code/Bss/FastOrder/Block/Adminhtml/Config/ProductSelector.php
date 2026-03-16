<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\FastOrder\Block\Adminhtml\Config;

/**
 * Class ProductSelector
 * @package Bss\FastOrder\Block\Adminhtml\Config
 */
class ProductSelector extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * ProductSelector constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
    }


    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $htmlId = $element->getId();
        $data = $element->getData();

        $data['after_element_js'] = $this->_afterElementJs($element);
        $data['after_element_html'] = $this->_afterElementHtml($element);
        $data['readonly'] = 'readonly';
        $htmlItem = $this->elementFactory->create('text', ['data' => $data]);
        $htmlItem
            ->setId("{$htmlId}")
            ->setForm($element->getForm())
            ->addClass('required-entry')
            ->addClass('entities');
        $return = <<<HTML
                <div id="{$htmlId}-container" class="chooser_container">{$htmlItem->getElementHtml()}</div>
HTML;
        $element->setData('after_element_html', $return);
        return $element->getElementHtml();
    }

    /**
     * @param $element
     * @return string
     */
    protected function _afterElementHtml($element)
    {
        $htmlId = $element->getId();
        $openChooserText = __('Open Chooser');
        $applyText = __('Apply');

        $return = <<<HTML
            <a href="javascript:void(0)" onclick="MultiProductChooser.displayChooser('{$htmlId}-container')" class="widget-option-chooser" title="{$openChooserText}">
                <img src="{$this->getViewFileUrl('images/rule_chooser_trigger.gif')}" alt="{$openChooserText}" />
            </a>
            <a href="javascript:void(0)" onclick="MultiProductChooser.hideChooser('{$htmlId}-container')" title="{$applyText}">
                <img src="{$this->getViewFileUrl('images/rule_component_apply.gif')}" alt="{$applyText}">
            </a>
            <div class="chooser product-chooser"></div>
HTML;
        return $return;
    }

    /**
     * @param $element
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _afterElementJs($element)
    {
        $chooserUrl = $this->getUrl('adminhtml/widget_instance/products', []);
        $htmlId = $element->getId();
        $return = <<<HTML
            <style>
                .chooser.product-chooser {
                    width: 130%;
                }
                .accordion .form-inline .config td {
                    padding: 1rem 1rem!important;
                }
                .admin__data-grid-pager-wrap select, .admin__data-grid-pager-wrap input[type='text'] {
                    width: auto!important;
                }
                .admin__data-grid-pager-wrap select[name=limit] {
                    margin-bottom: 15px;
                }
            </style>
            <script>
                    require([
                    'jquery',
                    'Magento_Ui/js/modal/alert',
                    "prototype"
                ], function (jQuery, alert) {
                    jQuery('#fastorder_prepopulated_product_product_selector.entities').removeAttr('readonly');
                    var MultiProductChooser = {
                        displayChooser : function(chooser) {
                            chooser  = $(chooser).down('div.chooser');
                            entities = chooser.up('div.chooser_container').down('input[type="text"].entities').value;
                            postParameters = {selected: entities};
                            url = '{$chooserUrl}';
                            if (chooser && url) {
                                if (chooser.innerHTML == '') {
                                        new Ajax.Request(url, {
                                        method  : 'post',
                                        parameters : postParameters,
                                        onSuccess  : function(transport) {
                                            try {
                                                if (transport.responseText) {
                                                    Element.insert(chooser, transport.responseText);
                                                    chooser.removeClassName('no-display');
                                                    chooser.show();
                                                }
                                            } catch (e) {
                                                alert({
                                                   content: 'Error occurs during loading chooser.'
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    chooser.removeClassName('no-display');
                                    chooser.show();
                                }
                            }
                       },
                        hideChooser : function(chooser) {
                                chooser = $(chooser).down('div.chooser');
                                if (chooser) {
                                chooser.addClassName('no-display');
                                chooser.hide();
                                }
                        },
                        addProductItemToSelection: function(groupId, item) {
                            if (!isNaN(parseInt(item))) {
                                this.selectedItems[groupId].set(item, 1);
                            }
                        },
                        checkProduct : function(event) {                            
                            var cont = document.querySelector(".product-chooser");
                            var elm = event.memo.element,
                            container = event.target.up('div.chooser').up('div.chooser_container');
                            var matches = cont.querySelector("tr.on-mouse");
                            var mat = matches ? matches.querySelector("td.col-entity_id") : null;
                            value = container.down('input[type="text"].entities').value.strip();
                            pickedId = mat ? mat.childNodes[0].nodeValue : null;
                            pickedId = pickedId ? pickedId.trim() : '';
                            if (elm.checked) {
                                if (value) ids = value.split(',');
                                else ids = [];

                                if (-1 === ids.indexOf(pickedId)) {
                                    ids.push(pickedId);
                                    container.down('input[type="text"].entities').value = ids.join(',');
                                }
                            } else {
                                ids = value.split(',');

                                while (-1 !== ids.indexOf(pickedId)) {
                                    ids.splice(ids.indexOf(pickedId), 1);
                                    container.down('input[type="text"].entities').value = ids.join(',');
                                }
                            }                          
                          }
                      };
                    
                    window.MultiProductChooser = MultiProductChooser;
                    jQuery(function() {
                        var container = $('{$htmlId}-container');
                        if (container) {
                            //container.up(0).down('.control-value').hide();
                        }              
                         Event.observe(document, 'product:changed', function(event){
                            MultiProductChooser.checkProduct(event);
                        });
                        Event.observe(document, 'category:beforeLoad', function(event) {
                                container = event.target.up('div.chooser_container');
                                value   = container.down('input[type="text"].entities').value.strip();
                            event.memo.treeLoader.baseParams.selected = value;
                        });
                    });
                });
            </script>
HTML;
        return $return;
    }
}
