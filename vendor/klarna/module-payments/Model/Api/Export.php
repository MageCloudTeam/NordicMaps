<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api;

/**
 * @internal
 */
trait Export
{

    /**
     * Exportable class fields
     *
     * @var array
     */
    public $exports = [];

    /**
     * Generate array object needed for API call
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        if (!is_array($this->exports)) {
            return $data;
        }
        foreach ($this->exports as $export) {
            if ($this->$export !== null) {
                $data[$export] = $this->$export;
            }
        }
        return $data;
    }
}
