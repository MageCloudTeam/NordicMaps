<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Configuration;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use PHPUnit\Framework\TestCase;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Configuration\ApiValidation
 */
class ApiValidationTest extends TestCase
{
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;
    /**
     * @var ApiValidation|object
     */
    private ApiValidation $apiValidation;
    /**
     * @var array|\PHPUnit\Framework\MockObject\MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var Store
     */
    private Store $store;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testIsKpEnabledSettingSetToTrue(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsKpEnabled')
            ->willReturn(true);
        static::assertTrue($this->apiValidation->isKpEnabled($this->store));
    }

    public function testIsKpEnabledSettingSetToFalse(): void
    {
        $validationHistory = ['Klarna Payments in not enabled'];

        $this->dependencyMocks['paymentConfig']->method('IsKpEnabled')
                ->willReturn(false);
        static::assertFalse($this->apiValidation->isKpEnabled($this->store));
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testIsKpEndpointSelectedEndpointSelected(): void
    {
        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_eu');
        static::assertTrue($this->apiValidation->isKpEndpointSelected($this->store));
    }

    public function testIsKpEndpointSelectedNoEndpointSelected(): void
    {
        $validationHistory = ['No Klarna Payments endpoint is selected'];

        $expectNoKpEndpointIsSelected = false;
        $isKpEndpointSelected = $this->apiValidation->isKpEndpointSelected($this->store);
        static::assertEquals($expectNoKpEndpointIsSelected, $isKpEndpointSelected);
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testIsKpEndpointSelectedForUsMarketEndpointForUsSelected(): void
    {
        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_na');
        static::assertTrue($this->apiValidation->isKpEndpointSelectedForUsMarket($this->store));
    }

    public function testIsKpEndpointSelectedForUsMarketEndpointDifferentThanUsSelected(): void
    {
        $validationHistory = ['The US endpoint is not selected'];

        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_eu');
        static::assertFalse($this->apiValidation->isKpEndpointSelectedForUsMarket($this->store));
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testIsKpEndpointSelectedForUsMarketNoEndpointSelected(): void
    {
        $validationHistory = ['No Klarna Payments endpoint is selected'];

        $expectNoKpEndpointIsSelected = false;
        $isKpEndpointSelected = $this->apiValidation->isKpEndpointSelectedForUsMarket($this->store);
        static::assertEquals($expectNoKpEndpointIsSelected, $isKpEndpointSelected);
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testSendApiRequestAllowedInvalidKpApiSettings(): void
    {
        $validationHistory = ['No Klarna Payments endpoint is selected'];

        $expectKpMisconfigured = false;
        $canKpRequestBeSent = $this->apiValidation->sendApiRequestAllowed($this->quote);
        static::assertFalse($expectKpMisconfigured, $canKpRequestBeSent);
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    public function testSendApiRequestAllowedAllCountriesAreAllowed(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsKpEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_eu');
        $this->dependencyMocks['paymentConfig']->method('getAllowedCountries')
            ->willReturn([]);

        static::assertTrue($this->apiValidation->sendApiRequestAllowed($this->quote));
    }

    public function testSendApiRequestAllowedTargetCountryIsAllowed(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsKpEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_eu');
        $this->dependencyMocks['paymentConfig']->method('getAllowedCountries')
            ->willReturn(['AT', 'DE', 'US']);

        static::assertTrue($this->apiValidation->sendApiRequestAllowed($this->quote));
    }

    public function testSendApiRequestAllowedTargetCountryIsNotAllowed(): void
    {
        $this->dependencyMocks['paymentConfig']->method('IsKpEnabled')
            ->willReturn(true);
        $this->dependencyMocks['apiConfiguration']->method('getEndpoint')
            ->willReturn('kp_eu');
        $this->dependencyMocks['paymentConfig']->method('getAllowedCountries')
            ->willReturn(['AT', 'US']);

        $validationHistory = ['Klarna Payments is not allowed to be shown for quote id: 1'];
        static::assertFalse($this->apiValidation->sendApiRequestAllowed($this->quote));
        static::assertEquals($validationHistory, $this->apiValidation->getFailedValidationHistory());
    }

    protected function setUp(): void
    {
        $this->mockFactory   = new MockFactory($this);
        $objectFactory       = new TestObjectFactory($this->mockFactory);
        $this->apiValidation = $objectFactory->create(ApiValidation::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->store = $this->mockFactory->create(Store::class);
        $this->quote = $this->mockFactory->create(Quote::class);
        $this->quote->method('getStore')
            ->willReturn($this->store);

        $address = $this->mockFactory->create(Address::class);
        $address->method('getCountryId')
            ->willReturn('DE');
        $this->quote->method('getShippingAddress')
            ->willReturn($address);
        $this->quote->method('getId')
            ->willReturn(1);
    }
}
