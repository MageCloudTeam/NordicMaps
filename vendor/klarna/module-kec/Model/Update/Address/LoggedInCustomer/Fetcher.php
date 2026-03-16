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
class Fetcher
{
    /**
     * @var Finder
     */
    private Finder $finder;
    /**
     * @var Repository
     */
    private Repository $repository;

    /**
     * @param Finder $finder
     * @param Repository $repository
     * @codeCoverageIgnore
     */
    public function __construct(Finder $finder, Repository $repository)
    {
        $this->finder = $finder;
        $this->repository = $repository;
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
            return $this->finder->findCustomerAddress($klarnaAddressData, $customer);
        } catch (KlarnaException $e) {
            return $this->repository->createEntry($klarnaAddressData, $customer);
        }
    }
}
