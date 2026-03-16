<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

use Magento\Framework\Phrase;

/**
 * Edit action.
 */
class Edit extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractEdit
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Shiprestriction::rule';

    /**
     * @var string
     */
    protected $registryKey = \Amasty\Shiprestriction\Model\ConstantsInterface::REGISTRY_KEY;

    /**
     * @return Phrase
     */
    protected function getDefaultTitle()
    {
        return __('Add new Shipping Restriction Rule');
    }

    /**
     * @param int $ruleId
     *
     * @return Phrase
     */
    protected function getErrorMessage($ruleId)
    {
        return __('Unable to load Shipping Restriction Rule with ID %1. Please review the log and try again.', $ruleId);
    }
}
