<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

/**
 * @internal
 */
class Fields
{
    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(RegionFactory $regionFactory, DataObjectFactory $dataObjectFactory)
    {
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Getting back the quote address fields filled by the Klarna address
     *
     * @param array $klarnaAddressInput
     * @return array
     */
    public function getQuoteAddressFieldsByKlarnaAddress(array $klarnaAddressInput): array
    {
        $klarnaAddressInstance = $this->dataObjectFactory->create(['data' => $klarnaAddressInput]);
        $country = strtoupper($klarnaAddressInstance->getCountry());

        $data = [
            'lastname'      => $klarnaAddressInstance->getFamilyName(),
            'firstname'     => $klarnaAddressInstance->getGivenName(),
            'email'         => $klarnaAddressInstance->getEmail(),
            'company'       => $klarnaAddressInstance->getOrganizationName(),
            'prefix'        => $klarnaAddressInstance->getTitle(),
            'street'        => $this->getStreetData($klarnaAddressInstance),
            'postcode'      => $klarnaAddressInstance->getPostalCode(),
            'city'          => $klarnaAddressInstance->getCity(),
            'region_id'     => (int)$this->regionFactory->create()->loadByCode(
                $klarnaAddressInstance->getRegion(),
                $country
            )->getId(),
            'region'        => $klarnaAddressInstance->getRegion(),
            'telephone'     => $klarnaAddressInstance->getPhone(),
            'country_id'    => $country
        ];

        if ($klarnaAddressInstance->hasCustomerDob()) {
            $data['dob'] = $klarnaAddressInstance->getCustomerDob();
        }

        if ($klarnaAddressInstance->hasCustomerGender()) {
            $data['gender'] = $klarnaAddressInstance->getCustomerGender();
        }

        return $data;
    }

    /**
     * Getting back the street data
     *
     * @param DataObject $klarnaAddressData
     * @return array
     */
    private function getStreetData(DataObject $klarnaAddressData): array
    {
        return array_filter(
            [
                $klarnaAddressData->getStreetAddress() . $klarnaAddressData->getHouseExtension(),
                $klarnaAddressData->getStreetAddress2(),
            ]
        );
    }
}
