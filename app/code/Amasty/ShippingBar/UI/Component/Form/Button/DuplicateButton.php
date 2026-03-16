<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\UI\Component\Form\Button;

class DuplicateButton extends AbstractButton
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->isAllowed()) {
            $data =  [
                'label' => __('Duplicate'),
                'class' => 'duplicate',
                'sort_order' => 30,
                'url' => $this->getUrl('*/*/duplicate', ['id' => $this->getCurrentId()])
            ];
        }

        return $data;
    }
}
