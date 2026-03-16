<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ExpressRates\Test\Integration\Model\Config;

use Dhl\ExpressRates\Model\Config\ModuleConfig;
use Dhl\ExpressRates\Model\Config\ModuleConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ModuleConfigTest extends TestCase
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

        $this->config = $this->objectManager->create(ModuleConfig::class);
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/active 0
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/active 1
     */
    public function isEnabled()
    {
        self::assertEquals(0, $this->config->isEnabled());
        self::assertEquals(1, $this->config->isEnabled('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/sort_order 10
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/sort_order 20
     */
    public function getSortOrder()
    {
        self::assertEquals(10, $this->config->getSortOrder());
        self::assertEquals(20, $this->config->getSortOrder('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/title DHL Express
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/title Title
     */
    public function getTitle()
    {
        self::assertEquals('DHL Express', $this->config->getTitle());
        self::assertEquals('Title', $this->config->getTitle('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/emulated_carrier flatrate
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/emulated_carrier tablerate
     */
    public function getEmulatedCarrier()
    {
        self::assertEquals('flatrate', $this->config->getEmulatedCarrier());
        self::assertEquals('tablerate', $this->config->getEmulatedCarrier('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/sallowspecific 0
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/sallowspecific 1
     */
    public function shipToSpecificCountries()
    {
        self::assertEquals(0, $this->config->shipToSpecificCountries());
        self::assertEquals(1, $this->config->shipToSpecificCountries('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/specificcountry AL,DZ
     */
    public function getSpecificCountries()
    {
        $euCountries = $this->config->getSpecificCountries();
        self::assertNotEmpty($euCountries);
        self::assertCount(2, $euCountries);
        self::assertContains('AL', $euCountries);
        self::assertContains('DZ', $euCountries);
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/specificerrmsg Error message
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/specificerrmsg Special error message
     */
    public function getErrorMessage()
    {
        self::assertEquals('Error message', $this->config->getNotApplicableErrorMessage());
        self::assertEquals('Special error message', $this->config->getNotApplicableErrorMessage('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/username Username
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/username userName
     */
    public function getUserName()
    {
        self::assertEquals('Username', $this->config->getUserName());
        self::assertEquals('userName', $this->config->getUserName('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/password testcase1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/password testcase2
     */
    public function getPassword()
    {
        /** @var EncryptorInterface $encryptor */
        $encryptor = $this->objectManager->get(EncryptorInterface::class);

        self::assertSame($encryptor->decrypt('testcase1'), $this->config->getPassword());
        self::assertSame($encryptor->decrypt('testcase2'), $this->config->getPassword('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/loglevel 400
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/loglevel 200
     * @magentoConfigFixture secondstore_store carriers/dhlexpress/loglevel 100
     */
    public function getLogLevel()
    {
        self::assertEquals('400', $this->config->getLogLevel());
        self::assertEquals('100', $this->config->getLogLevel('secondstore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/accountnumber 987654321
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/accountnumber 123456789
     */
    public function getAccountNumber()
    {
        self::assertEquals('987654321', $this->config->getAccountNumber());
        self::assertEquals('123456789', $this->config->getAccountNumber('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/logging 0
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/logging 1
     */
    public function getLogging()
    {
        self::assertEquals(0, $this->config->isLoggingEnabled());
        self::assertEquals(1, $this->config->isLoggingEnabled('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/regular_pickup 1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/regular_pickup 0
     */
    public function getRegularPickup()
    {
        self::assertEquals(1, $this->config->isRegularPickup());
        self::assertEquals(0, $this->config->isRegularPickup('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/pickup_time 00,00,00
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/pickup_time 12,07,10
     */
    public function getPickupTime()
    {
        self::assertEquals('00,00,00', $this->config->getPickupTime());
        self::assertEquals('12,07,10', $this->config->getPickupTime('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store      carriers/dhlexpress/domestic_handling_type F
     * @magentoConfigFixture current_store      carriers/dhlexpress/domestic_affect_rates 1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/domestic_affect_rates 1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/domestic_handling_type P
     */
    public function getDomesticHandlingType()
    {
        self::assertSame(AbstractCarrier::HANDLING_TYPE_FIXED, $this->config->getDomesticHandlingType());
        self::assertSame(
            AbstractCarrier::HANDLING_TYPE_PERCENT,
            $this->config->getDomesticHandlingType('fixturestore')
        );
    }

    /**
     * @test
     * @magentoConfigFixture current_store          carriers/dhlexpress/domestic_handling_fee 0
     * @magentoConfigFixture current_store          carriers/dhlexpress/domestic_affect_rates 1
     * @magentoConfigFixture fixturestore_store     carriers/dhlexpress/domestic_affect_rates 1
     * @magentoConfigFixture fixturestore_store     carriers/dhlexpress/domestic_handling_fee_percentage 30
     * @magentoConfigFixture fixturestore_store     carriers/dhlexpress/domestic_handling_type P
     */
    public function getDomesticHandlingFee()
    {
        self::assertSame(0.0, $this->config->getDomesticHandlingFee());
        self::assertSame(30.0, $this->config->getDomesticHandlingFee('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store      carriers/dhlexpress/international_affect_rates 1
     * @magentoConfigFixture current_store      carriers/dhlexpress/international_handling_type F
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/international_affect_rates 1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/international_handling_type P
     */
    public function getInternationalHandlingType()
    {
        self::assertSame(AbstractCarrier::HANDLING_TYPE_FIXED, $this->config->getInternationalHandlingType());
        self::assertSame(
            AbstractCarrier::HANDLING_TYPE_PERCENT,
            $this->config->getInternationalHandlingType('fixturestore')
        );
    }

    /**
     * @test
     * @magentoConfigFixture current_store      carriers/dhlexpress/international_affect_rates 1
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/international_affect_rates 1
     * @magentoConfigFixture current_store      carriers/dhlexpress/international_handling_fee 0
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/international_handling_type P
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/international_handling_fee_percentage 30
     */
    public function getInternationalHandlingFee()
    {
        self::assertSame(0.0, $this->config->getInternationalHandlingFee());
        self::assertSame(30.0, $this->config->getInternationalHandlingFee('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/alloweddomesticproducts I,O,1,N
     */
    public function getAllowedDomesticProducts()
    {
        $productKeys = $this->config->getAllowedDomesticProducts();
        self::assertTrue(is_array($productKeys));
        self::assertNotEmpty($productKeys);
        self::assertContainsOnly('string', $productKeys);
        self::assertCount(4, $productKeys);
        self::assertContains('I', $productKeys);
        self::assertContains('O', $productKeys);
        self::assertContains('1', $productKeys);
        self::assertContains('N', $productKeys);
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/allowedinternationalproducts X,D;P,K;E,L;M,T;Y
     */
    public function getAllowedInternationalProducts()
    {
        $productKeys = $this->config->getAllowedInternationalProducts();
        self::assertTrue(is_array($productKeys));
        self::assertNotEmpty($productKeys);
        self::assertContainsOnly('string', $productKeys);
        self::assertCount(9, $productKeys);
        self::assertContains('X', $productKeys);
        self::assertContains('D', $productKeys);
        self::assertContains('P', $productKeys);
        self::assertContains('K', $productKeys);
        self::assertContains('E', $productKeys);
        self::assertContains('L', $productKeys);
        self::assertContains('M', $productKeys);
        self::assertContains('T', $productKeys);
        self::assertContains('Y', $productKeys);
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/round_prices_mode no_rounding
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/round_prices_mode round_up
     * @magentoConfigFixture secondstore_store carriers/dhlexpress/round_prices_mode round_off
     */
    public function getRoundedPricesMode()
    {
        self::assertEquals('no_rounding', $this->config->getRoundedPricesMode());
        self::assertEquals('round_up', $this->config->getRoundedPricesMode('fixturestore'));
        self::assertEquals('round_off', $this->config->getRoundedPricesMode('secondstore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/round_prices_mode no_rounding
     * @magentoConfigFixture fixturestore_store  carriers/dhlexpress/round_prices_format full_price
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/round_prices_mode round_up
     * @magentoConfigFixture secondstore_store  carriers/dhlexpress/round_prices_format full_price
     * @magentoConfigFixture secondstore_store carriers/dhlexpress/round_prices_mode round_off
     */
    public function roundUp()
    {
        self::assertFalse($this->config->roundUp('current'));
        self::assertTrue($this->config->roundUp('fixturestore'));
        self::assertFalse($this->config->roundUp('secondstore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store      carriers/dhlexpress/round_prices_mode no_rounding
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/round_prices_mode round_up
     * @magentoConfigFixture secondstore_store  carriers/dhlexpress/round_prices_format full_price
     * @magentoConfigFixture secondstore_store  carriers/dhlexpress/round_prices_mode round_off
     */
    public function roundOff()
    {
        self::assertFalse($this->config->roundOff('current'));
        self::assertFalse($this->config->roundOff('fixturestore'));
        self::assertTrue($this->config->roundOff('secondstore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/round_prices_format full_price
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/round_prices_format static_decimal
     */
    public function getRoundedPricesFormat()
    {
        self::assertEquals('full_price', $this->config->getRoundedPricesFormat());
        self::assertEquals('static_decimal', $this->config->getRoundedPricesFormat('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/round_prices_static_decimal 0
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/round_prices_static_decimal 10
     * @magentoConfigFixture secondstore_store carriers/dhlexpress/round_prices_static_decimal 25
     */
    public function getRoundedPricesStaticDecimal()
    {
        self::assertEquals(0.00, $this->config->getRoundedPricesStaticDecimal('current'));
        self::assertEquals(0.1, $this->config->getRoundedPricesStaticDecimal('fixturestore'));
        self::assertEquals(0.25, $this->config->getRoundedPricesStaticDecimal('secondstore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/dhlexpress/terms_of_trade DTP/DDP
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/terms_of_trade DDU/DAP
     */
    public function getTermsOfTrade()
    {
        self::assertEquals('DTP/DDP', $this->config->getTermsOfTrade());
        self::assertEquals('DDU/DAP', $this->config->getTermsOfTrade('fixturestore'));
    }

    /**
     * @magentoConfigFixture current_store scarriers/dhlexpress/cut_off_time 00,00,00
     * @magentoConfigFixture fixturestore_store carriers/dhlexpress/cut_off_time 12,07,10
     */
    public function getCutOffTime()
    {
        self::assertEquals('00,00,00', $this->config->getCutOffTime());
        self::assertEquals('12,07,10', $this->config->getCutOffTime('fixturestore'));
    }
}
