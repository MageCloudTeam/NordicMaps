<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Controller\Adminhtml\Rule;

class Save extends \Amasty\CommonRules\Controller\Adminhtml\Rule\AbstractSave
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
    protected $dataPersistorKey = \Amasty\Shiprestriction\Model\ConstantsInterface::DATA_PERSISTOR_FORM;

    protected function prepareData(&$data)
    {
        if (isset($data['rule_id'])) {
            unset($data['rule_id']);
        }

        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }

        unset($data['rule']);
    }
}
