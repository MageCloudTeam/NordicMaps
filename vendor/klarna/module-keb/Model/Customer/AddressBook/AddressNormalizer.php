<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\Customer\AddressBook;

/**
 * @internal
 */
class AddressNormalizer
{
    /**
     * Normalizing the address data
     *
     * @param array $address
     * @param array $mapping
     * @return array
     */
    public function normalizeAddressByMapping(array $address, array $mapping): array
    {
        $address[$mapping['telephone']] = str_replace(' ', '', $address[$mapping['telephone']]);
        $address[$mapping['telephone']] = str_replace('+', '', $address[$mapping['telephone']]);

        return array_map(function ($value) {
            return $value ?: '';
        }, $address);
    }
}
