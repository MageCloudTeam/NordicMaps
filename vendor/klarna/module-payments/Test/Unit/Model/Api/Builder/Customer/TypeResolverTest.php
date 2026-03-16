<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Customer;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Customer\TypeResolver;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass  \Klarna\Kp\Model\Api\Builder\Customer\TypeResolver
 */
class TypeResolverTest extends TestCase
{

    /**
     * @var TypeResolver
     */
    private TypeResolver $model;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;
    /**
     * @var MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $shippingAddress;

    /**
     * @var Address
     */
    private Address $billingAddress;

    /**
     * @dataProvider addressDifferentStatesDataProvider
     *
     * @param array $address
     * @param string $expected
     * @return void
     */
    public function testWhenB2bIsEnabledExpectedResultsAreVaryBasedOnShippingCompanyAndBillingCompanyValues(
        array $address,
        string $expected
    ): void {
        $this->dependencyMocks['paymentConfig']->method('isB2bEnabled')->willReturn(true);

        $this->shippingAddress->method('getCompany')->willReturn($address['shipping_address_company']);
        $this->billingAddress->method('getCompany')->willReturn($address['billing_address_company']);

        static::assertEquals($expected, $this->model->getData($this->quote));
    }

    /**
     * @dataProvider addressDifferentStatesDataProvider
     *
     * @param array $address
     * @return void
     */
    public function testWhenB2bIsDisabledPurchaseWillNotMarkAsB2bAnyway(array $address): void
    {
        $this->dependencyMocks['paymentConfig']->method('isB2bEnabled')
            ->willReturn(false);

        $this->shippingAddress->method('getCompany')
            ->willReturn($address['shipping_address_company']);
        $this->billingAddress->method('getCompany')
            ->willReturn($address['billing_address_company']);

        // since the b2b is disabled, the expected result is always 'person'
        $expected = 'person';

        static::assertEquals($expected, $this->model->getData($this->quote));
    }

    /**
     *
     * @return array
     */
    protected function addressDifferentStatesDataProvider(): array
    {
        return [
            [
                'address' => [
                    'shipping_address_company' => null,
                    'billing_address_company' => null,
                ],
                'expected' => 'person',
            ],
            [
                'address' => [
                    'shipping_address_company' => 'a-random-value',
                    'billing_address_company' => null,
                ],
                'expected' => 'person',
            ],
            [
                'address' => [
                    'shipping_address_company' => null,
                    'billing_address_company' => 'completely-random-value',
                ],
                'expected' => 'organization',
            ],
            [
                'address' => [
                    'shipping_address_company' => 'a-random-value',
                    'billing_address_company' => 'completely-random-value',
                ],
                'expected' => 'organization',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(TypeResolver::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->quote = $this->mockFactory->create(Quote::class);

        $this->shippingAddress = $this->mockFactory->create(Address::class);
        $this->billingAddress = $this->mockFactory->create(Address::class);

        $this->quote->method('getShippingAddress')->willReturn($this->shippingAddress);
        $this->quote->method('getBillingAddress')->willReturn($this->billingAddress);

        $store = $this->mockFactory->create(Store::class);
        $this->quote->method('getStore')
            ->willReturn($store);
    }
}
