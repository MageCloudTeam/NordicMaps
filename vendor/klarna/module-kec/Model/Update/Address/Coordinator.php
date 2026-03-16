<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address;

use Klarna\Kec\Model\Update\Address\LoggedInCustomer\Fetcher;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Coordinator
{
    /**
     * @var Guest
     */
    private Guest $guest;
    /**
     * @var Fetcher
     */
    private Fetcher $fetcher;

    /**
     * @param Guest $guest
     * @param Fetcher $fetcher
     * @codeCoverageIgnore
     */
    public function __construct(Guest $guest, Fetcher $fetcher)
    {
        $this->guest = $guest;
        $this->fetcher = $fetcher;
    }

    /**
     * Updating the address for the guest or logged in customer
     *
     * @param array $klarnaData
     * @param CartInterface $magentoQuote
     */
    public function updateAddress(array $klarnaData, CartInterface $magentoQuote): void
    {
        if ($magentoQuote->getCustomerIsGuest()) {
            $this->guest->applyKlarnaAddressData($klarnaData, $magentoQuote);
            return;
        }

        $this->updateLoggedInCustomerQuote($klarnaData, $magentoQuote);
    }

    /**
     * Updating the quote of a logged in customer
     *
     * @param array $addressData
     * @param CartInterface $quote
     * @throws \Exception
     */
    private function updateLoggedInCustomerQuote(array $addressData, CartInterface $quote): void
    {
        $customer = $quote->getCustomer();
        $customerAddress = $this->fetcher->getAddressFromCustomerOrCreate($addressData['shipping_address'], $customer);

        $quoteAddress = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $quoteAddress->importCustomerAddressData($customerAddress);
    }
}
