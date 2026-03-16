<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Block\Adminhtml\System\Config;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class Tooltip extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $tooltipConfig = [
                'tooltip' => [
                    'trigger' => '[data-tooltip-trigger=trigger]',
                    'action' => 'click',
                    'delay' => 0,
                    'track' => false,
                    'position' => 'top'
                ]
            ];

            //use object manager to avoid loading dependencies of parent class
            $objectManager = ObjectManager::getInstance();
            $serializer = $objectManager->create(Json::class);

            $tooltipConfig = str_replace('"', "'", $serializer->serialize($tooltipConfig));

            $html .= '<div data-bind="' . $tooltipConfig . '" class="hidden">' . $element->getTooltip() . '</div>';
            $html .= '<div class="tooltip" data-tooltip-trigger="trigger"><span class="help"><span></span></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }
}
