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
class UpdateQuoteEmailTest extends ControllerTestCase
{

    /**
     * @magentoDataFixture Klarna_Base::Test/Integration/_files/fixtures/customer_us_with_address_same_billing_shipping.php
     *
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoUpdateSinceTheCustomerIsLoggedIn(): void
    {
        $quote = $this->session->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $oldEmailValue = $billingAddress->getEmail();
        $newEmailValue = 'kkk@kkk.de';

        $this->sendRequest(
            ['email' => $newEmailValue],
            'checkout/klarna/updateQuoteEmail',
            Http::METHOD_POST
        );

        static::assertNotEquals($newEmailValue, $billingAddress->getEmail());
        static::assertEquals($oldEmailValue, $billingAddress->getEmail());
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteUpdateSinceCustomerIsGuest(): void
    {
        $quote = $this->session->getQuote();
        $oldEmailValue = 'aaa@aaa.de';
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setEmail($oldEmailValue);
        $newEmailValue = 'kkk@kkk.de';

        $this->sendRequest(
            ['email' => $newEmailValue],
            'checkout/klarna/updateQuoteEmail',
            Http::METHOD_POST
        );

        static::assertNotEquals($oldEmailValue, $billingAddress->getEmail());
        static::assertEquals($newEmailValue, $billingAddress->getEmail());
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoUpdateForGuestSinceEmailParameterNotGiven(): void
    {
        $quote = $this->session->getQuote();
        $oldEmailValue = 'aaa@aaa.de';
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setEmail($oldEmailValue);

        $this->sendRequest(
            [],
            'checkout/klarna/updateQuoteEmail',
            Http::METHOD_POST
        );

        static::assertEquals($oldEmailValue, $quote->getBillingAddress()->getEmail());
    }
}
