<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\Customer\AddressBook;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class AddressRepository
{
    /**
     * @var AddressFactory
     */
    private AddressFactory $addressFactory;
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @param AddressFactory $addressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @codeCoverageIgnore
     */
    public function __construct(AddressFactory $addressFactory, AddressRepositoryInterface $addressRepository)
    {
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Creating a new address
     *
     * @param array $addressData
     * @param CustomerInterface $customer
     * @return AddressInterface
     */
    public function createEntry(array $addressData, CustomerInterface $customer): AddressInterface
    {
        $mapping = AddressComparator::KLARNA_ADDRESS_FIELD_MAPPING;
        $address = $this->addressFactory->createFromData($addressData, $mapping, $customer);

        return $this->addressRepository->save($address);
    }
}
