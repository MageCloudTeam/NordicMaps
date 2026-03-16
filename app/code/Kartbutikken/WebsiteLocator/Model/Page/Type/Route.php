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
 * Class Route
 */
class Route extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $urlPath = ltrim($this->getRequest()->getPathInfo(), '/');
        $returnUrl = $this->getTargetStore()->getBaseUrl();

        return $returnUrl . $urlPath;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->getRequest()->getPathInfo() === $this->getRequest()->getOriginalPathInfo();
    }
}
