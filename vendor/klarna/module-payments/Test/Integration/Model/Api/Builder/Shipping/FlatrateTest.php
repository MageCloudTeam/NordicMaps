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
class FlatrateTest extends RequestBuilderTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/product_simple.php
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php
     *
     * @magentoConfigFixture current_store tax/classes/shipping_tax_class 2
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCheckCorrectAttributeValuesForShopSetup1()
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
        $this->validator->performAllGeneralUsChecks($request, $quote);

        $orderLines = $request['order_lines'];
        $shippingItem = $this->validator->getShippingOrderlineItem($orderLines);
        static::assertSame('flatrate_flatrate', $shippingItem['reference']);
        static::assertSame('shipping_fee', $shippingItem['type']);
        static::assertSame(1, $shippingItem['quantity']);
        static::assertSame(round(500), $shippingItem['total_amount']);
    }
}
