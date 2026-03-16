<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

namespace MageCloud\VideoWidget\Model;
 
use Magento\Framework\Data\OptionSourceInterface;

class VideoSource implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => 'Youtube'],
            ['value' => 1, 'label' => 'Vimeo']
        ];
    }
}
