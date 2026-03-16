<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\MagicZoomPlus\Plugin;

use MagicToolbox\MagicZoomPlus\Classes\MagicZoomPlusModuleCoreClass as Subject;

/**
 * Class AddTranslations
 */
class AddTranslations
{
    /**
     * @var array
     */
    private $translated;

    /**
     * AddTranslations constructor.
     *
     * @param array $translated
     */
    public function __construct(array $translated = [])
    {
        $this->translated = $translated;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @param $id
     * @param $profile
     * @param $strict
     */
    public function afterGetParam(
        Subject $subject,
        $result,
        $id,
        $profile,
        $strict
    ) {
        if (in_array($id, $this->translated)) {
            return __($result);
        }

        return $result;
    }
}