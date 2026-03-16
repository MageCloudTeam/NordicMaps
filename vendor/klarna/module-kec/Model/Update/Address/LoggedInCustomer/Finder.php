<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address\LoggedInCustomer;

use Klarna\Base\Exception as KlarnaException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * @internal
 */
class Finder
{
    /**
     * @var Normalizer
     */
    private Normalizer $normalizer;
    /**
     * @var Comparator
     */
    private Comparator $comparator;

    /**
     * @param Normalizer $normalizer
     * @param Comparator $comparator
     * @codeCoverageIgnore
     */
    public function __construct(Normalizer $normalizer, Comparator $comparator)
    {
        $this->normalizer = $normalizer;
        $this->comparator = $comparator;
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
        $normalizedKlarnaData = $this->normalizer->normalizeKlarnaAddress(
            $klarnaAddressData
        );

        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
            if ($this->comparator->isApiAddressSameAsCustomerAddress($normalizedKlarnaData, $address)) {
                return $address;
            }
        }

        throw new KlarnaException(__('No address found'));
    }
}
