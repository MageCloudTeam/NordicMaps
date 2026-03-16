<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder\Coupons;

use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @internal
 */
class PercentageTest extends RequestBuilderTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_12_34_discount.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalForShopSetup1()
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
        $orderLines = $request['order_lines'];

        $this->validator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->validator->isKlarnaShopUsTaxSame($orderLines, $quote);
        $this->validator->isKlarnaShopShippingPriceSame($orderLines, $quote);
        $this->validator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_100_discount.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponForShopSetup1()
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
        $orderLines = $request['order_lines'];

        $this->validator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->validator->isKlarnaShopUsTaxSame($orderLines, $quote);
        $this->validator->isKlarnaShopShippingPriceSame($orderLines, $quote);
        $this->validator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
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
        $this->validator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
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
        $this->validator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default GB
     * @magentoConfigFixture current_store general/store_information/country_id GB
     * @magentoConfigFixture current_store tax/defaults/country GB
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id GB
     * @magentoConfigFixture current_store shipping/origin/region_id Greater London
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('GBP');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUkAddressData();
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
        $this->validator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default GB
     * @magentoConfigFixture current_store general/store_information/country_id GB
     * @magentoConfigFixture current_store tax/defaults/country GB
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 0
     * @magentoConfigFixture current_store tax/calculation/discount_tax 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id GB
     * @magentoConfigFixture current_store shipping/origin/region_id Greater London
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('GBP');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUkAddressData();
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
        $this->validator->performAllGeneralChecks($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_12_34_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/calculation/apply_after_discount 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCouponLowerGrandTotalForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
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
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->validator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(104, $fullTotalTaxAmount);
        static::assertEquals(104, $orderTaxAmount);
        $this->validator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->validator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/cart_percent_100_discount.php
     *
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store general/store_information/country_id DE
     * @magentoConfigFixture current_store tax/defaults/country DE
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/shipping_includes_tax 1
     * @magentoConfigFixture current_store tax/calculation/discount_tax 1
     * @magentoConfigFixture current_store tax/calculation/apply_after_discount 0
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store shipping/origin/country_id DE
     * @magentoConfigFixture current_store shipping/origin/region_id 82
     * @magentoConfigFixture current_store tax/display/shipping 2
     * @magentoConfigFixture current_store tax/display/type 2
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoConfigFixture current_store klarna/api/api_version kp_eu
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testFullCouponForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('simple');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
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
        $orderLines = $request['order_lines'];
        $fullTotalTaxAmount = $this->validator->getFullTotalTaxAmount($orderLines);
        $orderTaxAmount = $request['order_tax_amount'];

        static::assertEquals(38, $fullTotalTaxAmount);
        static::assertEquals(38, $orderTaxAmount);
        $this->validator->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->validator->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
    }
}
