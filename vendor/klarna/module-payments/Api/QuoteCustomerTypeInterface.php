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
interface QuoteCustomerTypeInterface
{

    /**
     * Setting the customer type
     *
     * @param string $customerType
     * @return $this
     */
    public function setCustomerType(string $customerType): self;

    /**
     * Getting back the customer type
     *
     * @return string|null
     */
    public function getCustomerType(): ?string;
}
