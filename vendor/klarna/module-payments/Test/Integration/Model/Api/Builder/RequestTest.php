<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @internal
 */
class RequestTest extends RequestBuilderTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestLoggedInCustomerWithBothDefaultAddressesAndVirtualCartReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestGuestCustomerAndVirtualCartReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGeneratePlaceOrderRequestEmptyQuoteImpliesThrowingException(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestLoggedInCustomerWithBothDefaultAddressesReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestLoggedInCustomerWithBothDefaultAddressesAndVirtualCartReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestGuestCustomerReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestGuestCustomerAndVirtualCartReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestEmptyQuoteImpliesThrowingException(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestLoggedInCustomerWithBothDefaultAddressesReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestLoggedInCustomerWithBothDefaultAddressesAndVirtualCartReturnsRequestWithValidAddresses(): void // phpcs:ignore
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestGuestCustomerReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $requestShippingAddress = $request['shipping_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertSame($requestBillingAddress, $requestShippingAddress);
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestGuestCustomerAndVirtualCartReturnsRequestWithValidAddresses(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $requestBillingAddress = $request['billing_address'];
        $quoteBillingAddress = $quote->getBillingAddress();

        static::assertTrue(!isset($request['shipping_address']));
        static::assertEquals($quoteBillingAddress->getFirstname(), $requestBillingAddress['given_name']);
        static::assertEquals($quoteBillingAddress->getLastname(), $requestBillingAddress['family_name']);
        static::assertEquals($quoteBillingAddress->getEmail(), $requestBillingAddress['email']);
        static::assertEquals(implode('', $quoteBillingAddress->getStreet()), $requestBillingAddress['street_address']);
        static::assertEquals($quoteBillingAddress->getCity(), $requestBillingAddress['city']);
        static::assertEquals($quoteBillingAddress->getPostcode(), $requestBillingAddress['postal_code']);
        static::assertEquals($quoteBillingAddress->getCountryId(), $requestBillingAddress['country']);
        static::assertEquals($quoteBillingAddress->getTelephone(), $requestBillingAddress['phone']);
    }

    /**
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateUpdateSessionRequestEmptyQuoteImpliesThrowingException(): void
    {
        $quote = $this->session->getQuote();

        self::expectException(KlarnaApiException::class);
        $this->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentLikeMethodGenerateUpdateSessionRequest(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $createSessionRequest = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $updateSessionRequest = $this
            ->requestBuilder
            ->generateUpdateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls']['authorization'],
            $updateSessionRequest['merchant_urls']['authorization']
        );
        static::assertEquals($createSessionRequest, $updateSessionRequest);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store klarna/api/api_version kp_na
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store payment/klarna_kp/data_sharing 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGenerateCreateSessionRequestSameContentWithPlaceOrderRequestForSameNodes(): void
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        $quote->setReservedOrderId(1);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);
        $quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $createSessionRequest = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $placeOrderRequest = $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();

        unset(
            $createSessionRequest['merchant_urls'],
            $placeOrderRequest['merchant_urls'],
            $placeOrderRequest['merchant_reference1'],
            $placeOrderRequest['merchant_reference2']
        );
        static::assertEquals($createSessionRequest, $placeOrderRequest);
    }
}
