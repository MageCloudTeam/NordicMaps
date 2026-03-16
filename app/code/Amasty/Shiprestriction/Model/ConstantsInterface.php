<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model;

interface ConstantsInterface
{
    public const REGISTRY_KEY = 'current_amasty_shiprestriction_rule';
    public const SECTION_KEY = 'amshiprestriction';
    public const DATA_PERSISTOR_FORM = 'amasty_shiprestriction_form_data';

    public const FIELDS = [
        'stores',
        'cust_groups',
        'methods',
        'days',
        'discount_id',
        'discount_id_disable'
    ];
}
