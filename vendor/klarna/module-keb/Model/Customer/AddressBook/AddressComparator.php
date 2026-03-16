<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\Customer\AddressBook;

use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class AddressComparator
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

    public const KLARNA_ADDRESS_FIELD_MAPPING = [
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'street' => 'street',
        'city' => 'city',
        'postcode' => 'postcode',
        'region_code' => 'region_code',
        'country_id' => 'country_id',
        'telephone' => 'telephone'
    ];

    /**
     * @var AddressNormalizer
     */
    private AddressNormalizer $addressNormalizer;

    /**
     * @param AddressNormalizer $addressNormalizer
     * @codeCoverageIgnore
     */
    public function __construct(AddressNormalizer $addressNormalizer)
    {
        $this->addressNormalizer = $addressNormalizer;
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

        $addressNormalized = $this->addressNormalizer->normalizeAddressByMapping(
            $addressFlat,
            self::MAGENTO_ADDRESS_FIELD_MAPPING
        );

        foreach (self::MAGENTO_ADDRESS_FIELD_MAPPING as $apiKey => $addressKey) {
            if ($normalizedKlarnaData[$apiKey] !== $addressNormalized[$addressKey]) {
                return false;
            }
        }

        return true;
    }
}
