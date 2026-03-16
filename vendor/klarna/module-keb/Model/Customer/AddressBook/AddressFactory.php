<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\Customer\AddressBook;

use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class AddressFactory
{
    /**
     * @var AddressInterfaceFactory
     */
    private AddressInterfaceFactory $magentoAddressFactory;

    /**
     * @param AddressInterfaceFactory $magentoAddressFactory
     * @codeCoverageIgnore
     */
    public function __construct(AddressInterfaceFactory $magentoAddressFactory)
    {
        $this->magentoAddressFactory = $magentoAddressFactory;
    }

    /**
     * Creating a address instance with filled data and returning it
     *
     * @param array $addressData
     * @param array $mapping
     * @param CustomerInterface $customer
     * @return AddressInterface
     */
    public function createFromData(array $addressData, array $mapping, CustomerInterface $customer): AddressInterface
    {
        $address = $this->magentoAddressFactory->create();
        $address->setFirstname($addressData[$mapping['firstname']]);
        $address->setLastname($addressData[$mapping['lastname']]);
        $address->setTelephone($addressData[$mapping['telephone']]);
        $address->setStreet($addressData[$mapping['street']]);

        $address->setCity($addressData[$mapping['city']]);
        $address->setCountryId($addressData[$mapping['country_id']]);
        $address->setPostcode($addressData[$mapping['postcode']]);
        $address->setRegionId($addressData[$mapping['region_code']]);
        $address->setCustomerId($customer->getId());
        $address->setIsDefaultShipping(1);
        $address->setIsDefaultBilling(1);

        return $address;
    }
}
