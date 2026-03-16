<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address\LoggedInCustomer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class Repository
{
    /**
     * @var Factory
     */
    private Factory $factory;
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @param Factory $factory
     * @param AddressRepositoryInterface $addressRepository
     * @codeCoverageIgnore
     */
    public function __construct(Factory $factory, AddressRepositoryInterface $addressRepository)
    {
        $this->factory = $factory;
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
        $address = $this->factory->createFromData($addressData, $customer);

        return $this->addressRepository->save($address);
    }
}
