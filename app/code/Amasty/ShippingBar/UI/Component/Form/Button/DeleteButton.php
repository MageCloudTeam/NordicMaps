<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\UI\Component\Form\Button;

class DeleteButton extends AbstractButton
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->isAllowed()) {
            $data =  [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete the bar'
                    ) . '\', \'' . $this->getUrl('*/*/delete', ['id' => $this->getCurrentId()]) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
