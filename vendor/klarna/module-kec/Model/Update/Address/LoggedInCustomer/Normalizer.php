<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address\LoggedInCustomer;

/**
 * @internal
 */
class Normalizer
{
    /**
     * Normalizing the Klarna address data
     *
     * @param array $address
     * @return array
     */
    public function normalizeKlarnaAddress(array $address): array
    {
        $address['phone'] = preg_replace('/[\s+]/', '', $address['phone']);

        return array_map(function ($value) {
            return $value ?: '';
        }, $address);
    }

    /**
     * Normalizing the address data
     *
     * @param array $address
     * @return array
     */
    public function normalizeShopAddress(array $address): array
    {
        $address['telephone'] = preg_replace('/[\s+]/', '', $address['telephone']);

        return array_map(function ($value) {
            return $value ?: '';
        }, $address);
    }
}
