<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model;

use Klarna\Base\Exception;
use Klarna\Base\Model\Quote\Address\FormFactory;
use Klarna\Keb\Model\Customer\AddressBook\AddressFetcher;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Base\Model\Quote\Address\Import;

/**
 * @internal
 */
class QuoteUpdater
{

    /**
     * @var AddressFetcher
     */
    private AddressFetcher $addressFetcher;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;
    /**
     * @var Shipping
     */
    private Shipping $shipping;
    /**
     * @var Import
     */
    private Import $import;
    /**
     * @var FormFactory
     */
    private FormFactory $formFactory;

    /**
     * @param Shipping $shipping
     * @param AddressFetcher $addressFetcher
     * @param CartRepositoryInterface $cartRepository
     * @param Import $import
     * @param FormFactory $formFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        Shipping $shipping,
        AddressFetcher $addressFetcher,
        CartRepositoryInterface $cartRepository,
        Import $import,
        FormFactory $formFactory
    ) {
        $this->shipping = $shipping;
        $this->addressFetcher = $addressFetcher;
        $this->cartRepository = $cartRepository;
        $this->import = $import;
        $this->formFactory = $formFactory;
    }

    /**
     * Updating the quote based on the given address data
     *
     * @param CartInterface $quote
     * @param array $addressData
     * @throws Exception
     */
    public function updateQuoteByAddressData(CartInterface $quote, array $addressData): void
    {
        if ($quote->getCustomerIsGuest()) {
            $this->updateGuestQuote($addressData, $quote);
            return;
        }

        $this->updateLoggedInCustomerQuote($addressData, $quote);
    }

    /**
     * Updating the quote of a guest
     *
     * @param array $addressData
     * @param CartInterface $quote
     * @throws Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateGuestQuote(array $addressData, CartInterface $quote): void
    {
        $this->import->importAddressFromRequest(
            $addressData,
            $this->formFactory->createCustomerAddressForm(),
            $quote->getBillingAddress()
        );
        $this->import->importAddressFromRequest(
            $addressData,
            $this->formFactory->createCustomerAddressForm(),
            $quote->getShippingAddress()
        );

        $quote->setCustomerEmail($addressData['email']);
        $this->shipping->setShippingMethodOnShippingAddress($quote);
        $this->cartRepository->save($quote);
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
        $address = $this->addressFetcher->getAddressFromCustomerOrCreate($addressData, $customer);

        if (!$quote->getIsVirtual()) {
            $quote->getShippingAddress()->importCustomerAddressData($address);
        }

        $quote->getBillingAddress()->importCustomerAddressData($address);
        $this->shipping->setShippingMethodOnShippingAddress($quote);
        $this->cartRepository->save($quote);
    }
}
