<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Controller\Klarna;

use Klarna\Base\Test\Integration\Helper\ControllerTestCase;
use Magento\Framework\App\Request\Http;

/**
 * @internal
 */
class CheckoutConfigTest extends ControllerTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteIndicatedShippingCountryIdValuedForGuest(): void
    {
        $this->prepareMagentoQuote();

        $targetValue = 'DE';
        $result = $this->sendRequest(
            ['shipping_country_id' => $targetValue],
            'checkout/klarna/checkoutConfig',
            Http::METHOD_POST
        );

        $quote = $this->session->getQuote();
        static::assertEquals($targetValue, $quote->getShippingAddress()->getCountryId());
        static::assertEquals($targetValue, $result['body']['shippingAddressFromData']['country_id']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteIndicatedShippingCompanyValueForGuest(): void
    {
        $this->prepareMagentoQuote();

        $targetValue = 'my_updated_company_value';
        $result = $this->sendRequest(
            ['shipping_company' => $targetValue],
            'checkout/klarna/checkoutConfig',
            Http::METHOD_POST
        );

        $quote = $this->session->getQuote();
        static::assertEquals($targetValue, $quote->getShippingAddress()->getCompany());
        static::assertEquals($targetValue, $result['body']['shippingAddressFromData']['company']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteIndicatedBillingCountryIdValueForGuest(): void
    {
        $this->prepareMagentoQuote();

        $targetValue = 'DE';
        $result = $this->sendRequest(
            ['billing_country_id' => $targetValue],
            'checkout/klarna/checkoutConfig',
            Http::METHOD_POST
        );

        $quote = $this->session->getQuote();
        static::assertEquals($targetValue, $quote->getBillingAddress()->getCountryId());
        static::assertEquals($targetValue, $result['body']['billingAddressFromData']['country_id']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteIndicatedBillingCompanyValueForGuest(): void
    {
        $this->prepareMagentoQuote();

        $targetValue = 'my_updated_company_value';
        $result = $this->sendRequest(
            ['billing_company' => $targetValue],
            'checkout/klarna/checkoutConfig',
            Http::METHOD_POST
        );

        $quote = $this->session->getQuote();
        static::assertEquals($targetValue, $quote->getBillingAddress()->getCompany());
        static::assertEquals($targetValue, $result['body']['billingAddressFromData']['company']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteIndicatedAllValuesForGuest(): void
    {
        $this->prepareMagentoQuote();

        $input = [
            'billing_company' => 'my_updated_billing_company',
            'billing_country_id' => 'my_updated_billing_country',
            'shipping_company' => 'my_updated_shipping_company',
            'shipping_country_id' => 'my_updated_shipping_country'
        ];
        $result = $this->sendRequest(
            $input,
            'checkout/klarna/checkoutConfig',
            Http::METHOD_POST
        );

        $quote = $this->session->getQuote();
        static::assertEquals($input['shipping_country_id'], $quote->getShippingAddress()->getCountryId());
        static::assertEquals($input['shipping_country_id'], $result['body']['shippingAddressFromData']['country_id']);
        static::assertEquals($input['shipping_company'], $quote->getShippingAddress()->getCompany());
        static::assertEquals($input['shipping_company'], $result['body']['shippingAddressFromData']['company']);
        static::assertEquals($input['billing_country_id'], $quote->getBillingAddress()->getCountryId());
        static::assertEquals($input['billing_country_id'], $result['body']['billingAddressFromData']['country_id']);
        static::assertEquals($input['billing_company'], $quote->getBillingAddress()->getCompany());
        static::assertEquals($input['billing_company'], $result['body']['billingAddressFromData']['company']);
    }

    private function prepareMagentoQuote()
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
    }
}
