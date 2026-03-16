<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\Controller\Adminhtml;

abstract class AbstractProfile extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_ShippingBar::bar_configuration';
}
