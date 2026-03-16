<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

/**
 * Class for getting html of selected Condition.
 */
class NewConditionHtml extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractCondition
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprestriction::rule';

    public function execute()
    {
        $this->newConditions('conditions');
    }
}
