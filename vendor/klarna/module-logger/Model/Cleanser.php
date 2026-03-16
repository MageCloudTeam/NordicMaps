<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model;

/**
 * @internal
 */
class Cleanser
{
    /**
     * @var array
     */
    public array $sensitiveKeys = [
        'password',
        'shared_secret',
        'secret',
        'date_of_birth',
        '_secret',
        'street',
        'Authorization',
        'given_name',
        'firstname',
        'gender',
        'family_name',
        'lastname',
        'email',
        'street_address',
        'phone',
        'telephone',
        'title',
        'postal_code',
        'city',
        'phone'
    ];
    /**
     * @var string
     */
    public string $replacement = '** REMOVED **';

    /**
     * Replace sensitive data with a replacement
     *
     * @param array $input
     * @return array
     */
    public function clean(array $input): array
    {
        array_walk_recursive(
            $input,
            function (&$value, $key) {
                if (in_array($key, $this->sensitiveKeys, true)) {
                    $value = $this->replacement;
                }
            }
        );

        return $input;
    }
}
