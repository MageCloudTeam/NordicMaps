<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model\Page\Type;

use Kartbutikken\WebsiteLocator\Model\Page\AbstractPage;

/**
 * Class Home
 */
class Home extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->getTargetStore()->getBaseUrl();
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return true;
    }
}
