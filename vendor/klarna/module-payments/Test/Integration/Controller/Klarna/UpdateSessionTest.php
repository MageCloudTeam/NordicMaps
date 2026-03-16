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
use Klarna\Kp\Model\Quote;
use Magento\Framework\App\Request\Http;
use Magento\Sales\Model\Order;

/**
 * @internal
 */
class UpdateSessionTest extends ControllerTestCase
{

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsWith400SinceNoAuthorizationTokenGiven(): void
    {
        $result = $this->sendRequest(
            [],
            'checkout/klarna/updateSession',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsWith400SinceEmptyAuthorizationTokenGiven(): void
    {
        $result = $this->sendRequest(
            ['authorization_token' => ''],
            'checkout/klarna/updateSession',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsWith400SinceNoKlarnaQuoteWasFoundWithAuthorizationToken(): void
    {
        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/updateSession',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsWith400SinceNoMagentoOrderWasFound(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setAuthCallbackActiveCurrentStatus(Quote::SUCCESSFUL);
        $klarnaQuote->setAuthorizationToken('aaa');
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(0);
        $klarnaQuote->setOrderId('1');
        $klarnaQuote->save();

        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/updateSession',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     * @magentoDataFixture Magento/Sales/_files/order.php
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestWasSuccessfulAndUpdatedCheckoutSession(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $magentoOrder = $this->objectManager->get(Order::class);
        $magentoOrder->loadByIncrementId('100000001');

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setAuthCallbackActiveCurrentStatus(Quote::SUCCESSFUL);
        $klarnaQuote->setAuthorizationToken('aaa');
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(0);
        $klarnaQuote->setOrderId($magentoOrder->getId());
        $klarnaQuote->save();

        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/updateSession',
            Http::METHOD_POST
        );

        static::assertEquals(204, $result['statusCode']);
        static::assertEquals($magentoQuote->getId(), $this->session->getLastQuoteId());
        static::assertEquals($magentoQuote->getId(), $this->session->getLastSuccessQuoteId());
        static::assertEquals($magentoOrder->getId(), $this->session->getLastOrderId());
        static::assertEquals($magentoOrder->getStatus(), $this->session->getLastOrderStatus());
        static::assertEquals('100000001', $this->session->getLastRealOrderId());
    }
}
