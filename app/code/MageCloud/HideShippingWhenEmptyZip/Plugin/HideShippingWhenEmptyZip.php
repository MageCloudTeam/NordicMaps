<?php
/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\HideShippingWhenEmptyZip\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;

class HideShippingWhenEmptyZip
{
    /**
     * @param ShipmentEstimationInterface $subject
     * @param \Closure $proccess
     * @param $cartId
     * @param AddressInterface $address
     */
    public function aroundEstimateByExtendedAddress(
        ShipmentEstimationInterface $subject,
        \Closure $proccess,
        $cartId,
        AddressInterface $address
    ) {
        if (empty($address->getPostcode())) {
            return [];
        }

        return $proccess($cartId, $address);
    }
}
