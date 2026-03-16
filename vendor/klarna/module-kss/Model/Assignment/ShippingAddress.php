<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Assignment;

use Magento\Framework\DataObject;

/**
 * @internal
 */
class ShippingAddress
{
    /**
     * Adding the kss pick up location address to the shipping address.
     * When no pick up location is chosen (for example "To Door Delivery") then we don't change the
     * shipping address since the correct values are already added to it before
     *
     * @param DataObject $klarnaRequest
     * @return DataObject
     */
    public function addKssAddressToShippingAddress(DataObject $klarnaRequest): DataObject
    {
        $shippingAddress = $klarnaRequest->getShippingAddress();

        $shippingOption = $klarnaRequest->getSelectedShippingOption();
        if (isset($shippingOption['delivery_details']['pickup_location'])) {
            $pickupAddress = $shippingOption['delivery_details']['pickup_location']['address'];
            $shippingAddress = array_merge($shippingAddress, $pickupAddress);
        }

        $klarnaRequest->setShippingAddress($shippingAddress);
        return $klarnaRequest;
    }
}
