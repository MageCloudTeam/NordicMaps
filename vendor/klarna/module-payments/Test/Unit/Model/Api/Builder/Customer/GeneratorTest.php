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
use Klarna\Kp\Model\Api\Builder\Customer\Generator;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass  \Klarna\Kp\Model\Api\Builder\Customer\Generator
 */
class GeneratorTest extends TestCase
{
    /**
     * @var Generator
     */
    private Generator $model;
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
    private Address $address;

    public function testGetBasicDataReturnsTypeValue(): void
    {
        $expected = 'company';
        $this->dependencyMocks['typeResolver']->method('getData')
            ->willReturn($expected);

        $result = $this->model->getBasicData($this->quote);
        static::assertEquals($expected, $result['type']);
    }

    public function testGetWithPrefilledDataReturnsTypeValue(): void
    {
        $expected = 'company';
        $this->dependencyMocks['typeResolver']->method('getData')
            ->willReturn($expected);

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertEquals($expected, $result['type']);
    }

    public function testGetWithPrefilledDataCustomerLoggedInCustomerAndNoDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataGuestCustomerAndNoDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(true);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataGuestCustomerButDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertTrue(!isset($result['date_of_birth']));
    }

    public function testGetWithPrefilledDataLoggedInCustomerAndDobValueGiven(): void
    {
        $this->quote->method('getCustomerIsGuest')
            ->willReturn(false);
        $this->quote->method('getCustomerDob')
            ->willReturn('1987-11-11');
        $this->dependencyMocks['dateTime']->method('date')
            ->with('Y-m-d', '1987-11-11')
            ->willReturn('1987-11-11');

        $result = $this->model->getWithPrefilledData($this->quote);
        static::assertEquals('1987-11-11', $result['date_of_birth']);
    }
    public function testIsPrefillAllowedDataSharingIsDisabled(): void
    {
        $this->dependencyMocks['paymentConfig']->method('isDataSharingEnabled')
            ->willReturn(false);

        static::assertFalse($this->model->isPrefillAllowed($this->quote));
    }

    public function testIsPrefillAllowedSelectedApiEndpointIsNotUS(): void
    {
        $this->dependencyMocks['paymentConfig']->method('isDataSharingEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiValidation']->method('isKpEndpointSelectedForUsMarket')
            ->willReturn(false);

        static::assertFalse($this->model->isPrefillAllowed($this->quote));
    }

    public function testIsPrefillAllowedCountryOfTheBillingAddressIsNotUS(): void
    {
        $this->dependencyMocks['paymentConfig']->method('isDataSharingEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiValidation']->method('isKpEndpointSelectedForUsMarket')
            ->willReturn(true);
        $this->address->method('getCountryId')
            ->willReturn('DE');

        static::assertFalse($this->model->isPrefillAllowed($this->quote));
    }

    public function testIsPrefillAllowedCountryOfTheBillingAddressIsUS(): void
    {
        $this->dependencyMocks['paymentConfig']->method('isDataSharingEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiValidation']->method('isKpEndpointSelectedForUsMarket')
            ->willReturn(true);
        $this->address->method('getCountryId')
            ->willReturn('US');

        static::assertTrue($this->model->isPrefillAllowed($this->quote));
    }

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(Generator::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $store = $this->mockFactory->create(Store::class);
        $this->address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(
            Quote::class,
            [
                'getCustomerIsGuest',
                'getCustomerDob',
                'getStore',
                'getBillingAddress',
            ]
        );
        $this->quote->method('getStore')
            ->willReturn($store);
        $this->quote->method('getBillingAddress')
            ->willReturn($this->address);
    }
}
