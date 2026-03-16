<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * @internal
 */
class Multiselect extends Value
{
    /**
     * Don't allow save with a value of -1
     *
     * @return Value
     */
    public function beforeSave(): Value
    {
        $value = $this->getValue();
        if ($value === '-1') {
            $this->setValue(null);
            return parent::beforeSave();
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        if (in_array('-1', $value, false)) {
            $this->setValue(null);
            return parent::beforeSave();
        }
        return parent::beforeSave();
    }
}
