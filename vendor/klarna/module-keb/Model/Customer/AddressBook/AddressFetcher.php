<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model\Customer\AddressBook;

use Klarna\Base\Exception as KlarnaException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class AddressFetcher
{
    /**
     * @var AddressFinder
     */
    private AddressFinder $addressFinder;
    /**
     * @var AddressRepository
     */
    private AddressRepository $addressRepository;

    /**
     * @param AddressFinder $addressFinder
     * @param AddressRepository $addressRepository
     * @codeCoverageIgnore
     */
    public function __construct(AddressFinder $addressFinder, AddressRepository $addressRepository)
    {
        $this->addressFinder = $addressFinder;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Getting back the address from the customer
     *
     * @param array $klarnaAddressData
     * @param CustomerInterface $customer
     * @return AddressInterface
     */
    public function getAddressFromCustomerOrCreate(
        array $klarnaAddressData,
        CustomerInterface $customer
    ): AddressInterface {
        try {
            return $this->addressFinder->findCustomerAddress($klarnaAddressData, $customer);
        } catch (KlarnaException $e) {
            return $this->addressRepository->createEntry($klarnaAddressData, $customer);
        }
    }
}
