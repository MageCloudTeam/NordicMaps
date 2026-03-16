<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Setup\Patch\Data;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Amasty\Shiprestriction\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ConvertConditionsToJson implements DataPatchInterface
{
    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    public function __construct(SerializedFieldDataConverter $fieldDataConverter)
    {
        $this->fieldDataConverter = $fieldDataConverter;
    }

    public function apply()
    {
        $this->fieldDataConverter->convertSerializedDataToJson(
            RuleResource::TABLE_NAME,
            'rule_id',
            ['conditions_serialized']
        );

        return $this;
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
