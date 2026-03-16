<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address\LoggedInCustomer;

use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class Factory
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
     * @param CustomerInterface $customer
     * @return AddressInterface
     */
    public function createFromData(array $addressData, CustomerInterface $customer): AddressInterface
    {
        $address = $this->magentoAddressFactory->create();
        $address->setFirstname($addressData['given_name']);
        $address->setLastname($addressData['family_name']);
        $address->setTelephone($addressData['phone']);
        $address->setStreet([$addressData['street_address']]);

        $address->setCity($addressData['city']);
        $address->setCountryId($addressData['country']);
        $address->setPostcode($addressData['postal_code']);
        $address->setCustomerId($customer->getId());
        $address->setIsDefaultShipping(1);
        $address->setIsDefaultBilling(1);

        return $address;
    }
}
