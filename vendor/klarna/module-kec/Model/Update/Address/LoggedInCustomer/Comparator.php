<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address\LoggedInCustomer;

use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class Comparator
{
    public const MAGENTO_ADDRESS_FIELD_MAPPING = [
        'firstname' => AddressInterface::FIRSTNAME,
        'lastname' => AddressInterface::LASTNAME,
        'street' => AddressInterface::STREET,
        'city' => AddressInterface::CITY,
        'postcode' => AddressInterface::POSTCODE,
        'region_code' => 'region_code',
        'country_id' => AddressInterface::COUNTRY_ID,
        'telephone' => AddressInterface::TELEPHONE
    ];

    /**
     * @var Normalizer
     */
    private Normalizer $normalizer;

    /**
     * @param Normalizer $normalizer
     * @codeCoverageIgnore
     */
    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Returns true if the Klarna api address is the same as the customer address
     *
     * @param array $normalizedKlarnaData
     * @param AddressInterface $address
     * @return bool
     */
    public function isApiAddressSameAsCustomerAddress(array $normalizedKlarnaData, AddressInterface $address): bool
    {
        $addressFlat = $address->__toArray();
        $addressFlat[self::MAGENTO_ADDRESS_FIELD_MAPPING['region_code']] = $address->getRegion()->getRegionCode();

        $addressFlat[self::MAGENTO_ADDRESS_FIELD_MAPPING['street']] = [
            'street1' => $addressFlat[self::MAGENTO_ADDRESS_FIELD_MAPPING['street']][0],
            'street2' => ''
        ];

        $normalizedShopAddress = $this->normalizer->normalizeShopAddress($addressFlat);

        $fields = [
            'given_name' => 'firstname',
            'family_name' => 'lastname',
            'phone' => 'telephone',
            'country' => 'country_id',
            'city' => 'city',
            'postal_code' => 'postcode',
            'street_address' => 'street'
        ];

        foreach ($fields as $apiKey => $addressKey) {
            if (is_array($normalizedShopAddress[$addressKey])) {
                $newValue = implode('', $normalizedShopAddress[$addressKey]);
                if ($normalizedKlarnaData[$apiKey] !== $newValue) {
                    return false;
                }
                continue;
            }
            if ($normalizedKlarnaData[$apiKey] !== $normalizedShopAddress[$addressKey]) {
                return false;
            }
        }

        return true;
    }
}
