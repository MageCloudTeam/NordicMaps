<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

namespace MageCloud\VideoWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class VideoWidget extends Template implements BlockInterface
{
    /**
     * @inheritdoc
     */
    protected $_template = "widget/videowidget.phtml";
}

