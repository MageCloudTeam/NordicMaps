<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes\Addresses;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Mapper;
use Klarna\Kp\Model\Payment\Kp;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Mapper
 */
class MapperTest extends TestCase
{
    /**
     * @var Mapper
     */
    private Mapper $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Address
     */
    private Address $address;
    /**
     * @var Store
     */
    private Store $store;

    public function testGetKlarnaDataFromAddressReturnsCorrectCityValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getCity')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['city']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectCountryValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getCountryId')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['country']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectEmailValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getEmail')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['email']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectFamilyNameValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getLastname')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['family_name']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectGivenNameValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getFirstname')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['given_name']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectPhoneValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getTelephone')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['phone']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectPostalCodeValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getPostcode')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['postal_code']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectRegionValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getRegionCode')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['region']);
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectStreetAddressValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getStreet')
            ->willReturn([$expected]);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['street_address']);
    }

    public function testGetKlarnaDataFromAddressDoesNotReturnStreetAddress2BecauseThereIsNoValue(): void
    {
        $this->address->method('getStreet')
            ->willReturn(['my_target_value']);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertTrue(!isset($result['street_address2']));
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectStreetAddress2Value(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getStreet')
            ->willReturn(['blub', $expected]);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['street_address2']);
    }

    public function testGetKlarnaDataFromAddressDoesNotReturnOrganizationNameBecauseItsB2c(): void
    {
        $this->address->method('getStreet')
            ->willReturn(['test']);
        $this->dependencyMocks['paymentConfig']->method('isB2bEnabled')
            ->willReturn(false);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertTrue(!isset($result['organization_name']));
    }

    public function testGetKlarnaDataFromAddressReturnsCorrectOrganizationNameValue(): void
    {
        $expected = 'my_target_value';
        $this->address->method('getCompany')
            ->willReturn($expected);
        $this->address->method('getStreet')
            ->willReturn(['test']);
        $this->dependencyMocks['paymentConfig']->method('isB2bEnabled')
            ->willReturn(true);

        $result = $this->model->getKlarnaDataFromAddress($this->store, $this->address);
        static::assertEquals($expected, $result['organization_name']);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(Mapper::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->store = $mockFactory->create(Store::class);
        $this->address = $mockFactory->create(Address::class);
    }
}
