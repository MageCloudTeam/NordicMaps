<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

/**
 * @api
 */
interface QuoteRedirectUrlInterface
{
    /**
     * Set the Klarna redirect url
     *
     * @param string $url
     * @return self
     */
    public function setRedirectUrl(string $url): self;

    /**
     * Get the Klarna redirect url
     *
     * @return string
     */
    public function getRedirectUrl(): string;
}
