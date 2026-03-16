<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Model\Store\Model\Address;

/**
 * Class Renderer
 */
class Renderer
{
    const TEMPLATE = "{{var street_line1}}\n" .
    "{{depend street_line2}}{{var street_line2}}\n{{/depend}}" .
    "{{depend city}}{{var city}},{{/depend}}{{var country}}";

}