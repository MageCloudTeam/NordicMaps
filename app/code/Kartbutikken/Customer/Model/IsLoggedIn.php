<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Customer\Model;

use Magento\Customer\Model\Context;

class IsLoggedIn
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $context;

    /**
     * IsLoggedIn constructor.
     *
     * @param \Magento\Framework\App\Http\Context $context
     */
    public function __construct(\Magento\Framework\App\Http\Context $context) {
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return !!$this->context->getValue(Context::CONTEXT_AUTH);
    }
}
