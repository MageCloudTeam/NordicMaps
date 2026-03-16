<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;

/**
 * @internal
 */
class Shipping
{
    /**
     * Sets shipping method for quote.
     *
     * @param Quote $quote
     * @return void
     * @throws \Exception
     */
    public function setShippingMethodOnShippingAddress(Quote $quote): void
    {
        $availableShippingRates = $this->getAvailableShippingRates($quote);
        if (empty($availableShippingRates)) {
            return;
        }

        $shippingMethodCode = $this->getShippingMethodCode($availableShippingRates);

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setShippingMethod($shippingMethodCode);
    }

    /**
     * Gets the available shipping rates grouped by carrier
     *
     * @param Quote $quote
     * @return Rate[]
     */
    private function getAvailableShippingRates(Quote $quote): array
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->collectShippingRates();

        return $shippingAddress->getGroupedAllShippingRates();
    }

    /**
     * Returns the shipping method code for the first carrier and first shipping rate
     *
     * @param Rate[] $availableShippingRates
     * @return string
     */
    private function getShippingMethodCode(array $availableShippingRates): string
    {
        $carrierName = array_key_first($availableShippingRates);
        /** @var Rate $shippingRate */
        $shippingRate       = $availableShippingRates[$carrierName][0];

        return $shippingRate->getCode();
    }
}
