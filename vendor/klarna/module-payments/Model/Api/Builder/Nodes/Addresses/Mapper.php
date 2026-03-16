<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes\Addresses;

use Klarna\Kp\Model\Configuration\Payment;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class Mapper
{
    /**
     * @var Payment
     */
    private Payment $paymentConfig;

    /**
     * @param Payment $paymentConfig
     * @codeCoverageIgnore
     */
    public function __construct(Payment $paymentConfig)
    {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Getting back the Klarna address data from the customer address object
     *
     * @param StoreInterface $store
     * @param Address $address
     * @return array
     */
    public function getKlarnaDataFromAddress(StoreInterface $store, Address $address): array
    {
        $result = [
            'city' => $address->getCity(),
            'country' => $address->getCountryId(),
            'email' => $address->getEmail(),
            'family_name' => $address->getLastname(),
            'given_name' => $address->getFirstname(),
            'phone' => $address->getTelephone(),
            'postal_code' => $address->getPostcode(),
            'region' => $address->getRegionCode(),
        ];

        $street = $address->getStreet();
        $result['street_address'] = $street[0];

        if (isset($street[1])) {
            $result['street_address2'] = $street[1];
        }

        if ($this->paymentConfig->isB2bEnabled($store)) {
            $result['organization_name'] = $address->getCompany();
        }

        return $result;
    }
}
