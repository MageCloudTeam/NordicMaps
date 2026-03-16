<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Request;

/**
 * @internal
 */
interface CustomerInterface
{
    /**
     * Setting the type
     *
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * ISO 8601 date: The customer date of birth.
     *
     * @param string $dob
     */
    public function setDateOfBirth(string $dob);

    /**
     * Gender (male or female)
     *
     * @param string $gender
     */
    public function setGender(string $gender);
}
