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
class AddressFinder
{
    /**
     * @var AddressNormalizer
     */
    private AddressNormalizer $addressNormalizer;
    /**
     * @var AddressComparator
     */
    private AddressComparator $addressComparator;

    /**
     * @param AddressNormalizer $addressNormalizer
     * @param AddressComparator $addressComparator
     * @codeCoverageIgnore
     */
    public function __construct(AddressNormalizer $addressNormalizer, AddressComparator $addressComparator)
    {
        $this->addressNormalizer = $addressNormalizer;
        $this->addressComparator = $addressComparator;
    }

    /**
     * Finding a address in the customer address book and returning it or throwing a exeption
     *
     * @param array $klarnaAddressData
     * @param CustomerInterface $customer
     * @return AddressInterface
     * @throws KlarnaException
     */
    public function findCustomerAddress(array $klarnaAddressData, CustomerInterface $customer): AddressInterface
    {
        $normalizedKlarnaData = $this->addressNormalizer->normalizeAddressByMapping(
            $klarnaAddressData,
            $this->addressComparator::KLARNA_ADDRESS_FIELD_MAPPING
        );

        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
            if ($this->addressComparator->isApiAddressSameAsCustomerAddress($normalizedKlarnaData, $address)) {
                return $address;
            }
        }

        throw new KlarnaException(__('No address found'));
    }
}
