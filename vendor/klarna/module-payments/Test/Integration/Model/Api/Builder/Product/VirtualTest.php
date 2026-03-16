<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model\Api\Builder\Product;

use Klarna\Base\Test\Integration\Helper\RequestBuilderTestCase;

/**
 * @internal
 */
class VirtualTest extends RequestBuilderTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSingleProductForShopSetup1()
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
        $this->validator->isKlarnaUsSumProductTotalsShopSubtotalSame($orderLines, $quote);
        $this->validator->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSippingOrderlineItemMissing($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
    public function testSingleProductForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
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
    public function testSingleProductForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('GBP');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUkAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testMultipleProductsForShopSetup1()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('USD');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUsAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaUsSumProductTotalsShopSubtotalSame($orderLines, $quote);
        $this->validator->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSippingOrderlineItemMissing($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
    public function testMultipleProductsForShopSetup2()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_uk_postal_W13_3BG.php
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
    public function testMultipleProductsForShopSetup3()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('GBP');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getUkAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testProductIsTypeDigitalInOrderlineListForShopSetup1()
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
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();

        $request = $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
        $orderLines = $request['order_lines'];
        $item = $this->validator->getAllProductOrderlineItems($orderLines)[0];

        self::assertSame('digital', $item['type']);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
    public function testSingleProductForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_virtual.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_de_postal_13055.php
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
    public function testMultipleProductsForShopSetup4()
    {
        $quote = $this->session->getQuote();
        $quote->setBaseCurrencyCode('EUR');

        $product = $this->productRepository->get('virtual-product');
        $quote->addProduct($product);
        $quote->addProduct($product);

        /** @var AddressInterface $address */
        $address = $this->dataProvider->getDeAddressData();
        $quote->setBillingAddress($address);
        $quote->setShippingAddress($address);

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
        $this->validator->isKlarnaShopTaxSame($orderLines, $quote);
        $this->validator->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->validator->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->validator->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->validator->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->validator->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }
}
