<?php

/**
 * See LICENSE.md for license details.

 */

declare(strict_types=1);

namespace Dhl\ExpressRates\Test\Integration\Model\Rate\Processor;

use Dhl\Express\Api\Data\ShippingProductsInterface;
use Dhl\ExpressRates\Model\Carrier\Express;
use Dhl\ExpressRates\Model\Config\ModuleConfig;
use Dhl\ExpressRates\Model\Config\ModuleConfigInterface;
use Dhl\ExpressRates\Model\Config\Source\DomesticProducts;
use Dhl\ExpressRates\Model\Rate\Processor\HandlingFee;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class HandlingFeeTest extends TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * @var DomesticProducts
     */
    private $domesticProducts;

    /**
     * Config fixtures are loaded before data fixtures. Config fixtures for
     * non-existent stores will fail. We need to set the stores up first manually.
     *
     * @link http://magento.stackexchange.com/a/93961
     */
    public static function setUpBeforeClass(): void
    {
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore_rollback.php');
        require realpath(
            TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore_rollback.php'
        );

        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore.php');
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore.php');

        parent::setUpBeforeClass();
    }

    /**
     * Delete manually added stores.
     *
     * @see setUpBeforeClass()
     */
    public static function tearDownAfterClass(): void
    {
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore_rollback.php');
        require realpath(
            TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore_rollback.php'
        );

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();

        $this->config           = $this->objectManager->create(ModuleConfig::class);
        $this->methodFactory    = $this->objectManager->create(MethodFactory::class);
        $this->domesticProducts = $this->objectManager->create(DomesticProducts::class);
    }

    /**
     * Test handling domestic fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_type F
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_fee_fixed  3
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_affect_rates 1
     */
    public function processMethodsWithFixedDomesticHandlingFee()
    {
        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier'       => Express::CARRIER_CODE,
                    'carrier_title' => 'TEST',
                    'method'        => ShippingProductsInterface::CODE_DOMESTIC,
                    'method_title'  => 'LABEL',
                    'price'         => 6.0,
                    'cost'          => 6.0,
                ]
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods     = $handlingFee->processMethods([ $method ]);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling international fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/dhlexpress/international_handling_type F
     * @magentoConfigFixture current_store carriers/dhlexpress/international_handling_fee_fixed  3
     * @magentoConfigFixture current_store carriers/dhlexpress/international_affect_rates 1
     */
    public function processMethodsWithFixedInternationalHandlingFee()
    {
        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier'       => Express::CARRIER_CODE,
                    'carrier_title' => 'TEST',
                    'method'        => ShippingProductsInterface::CODE_INTERNATIONAL_WORLDWIDE_DUTYFREE_WITHIN_EU,
                    'method_title'  => 'LABEL',
                    'price'         => 6.0,
                    'cost'          => 6.0,
                ]
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods     = $handlingFee->processMethods([ $method ]);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with percent handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_type P
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_fee_percentage  50
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_affect_rates 1
     */
    public function processMethodsWithPercentHandlingFee()
    {
        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier'       => Express::CARRIER_CODE,
                    'carrier_title' => 'TEST',
                    'method'        => ShippingProductsInterface::CODE_DOMESTIC,
                    'method_title'  => 'LABEL',
                    'price'         => 6.0,
                    'cost'          => 6.0,
                ]
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods     = $handlingFee->processMethods([ $method ]);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with fixed negative handling fee not dropping below 0.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_type F
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_handling_fee_fixed  -10
     * @magentoConfigFixture current_store carriers/dhlexpress/domestic_affect_rates 1
     */
    public function processMethodsWithFixedNegativeHandlingFee()
    {
        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier'       => Express::CARRIER_CODE,
                    'carrier_title' => 'TEST',
                    'method'        => ShippingProductsInterface::CODE_DOMESTIC,
                    'method_title'  => 'LABEL',
                    'price'         => 6.0,
                    'cost'          => 6.0,
                ]
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods     = $handlingFee->processMethods([ $method ]);

        self::assertSame(0.0, $methods[0]->getPrice());
        self::assertSame(0.0, $methods[0]->getCost());
    }
}
