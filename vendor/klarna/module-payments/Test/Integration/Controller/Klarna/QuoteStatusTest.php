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

/**
 * @internal
 */
class QuoteStatusTest extends ControllerTestCase
{

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsSinceNoAuthorizationTokenIsGiven(): void
    {
        $result = $this->sendRequest(
            [],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('1', $result['body']['is_active']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestFailsSinceAuthorizationTokenIsEmpty(): void
    {
        $result = $this->sendRequest(
            ['authorization_token' => ''],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('1', $result['body']['is_active']);
    }

    public function testExecuteRequestFailsSinceNoKlarnaQuoteWasFoundWithTheGivenAuthorizationToken(): void
    {
        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('1', $result['body']['is_active']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestWasSuccessfulSinceAuthorizationWorkflowHasTheStatusSuccessful(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setAuthCallbackActiveCurrentStatus(Quote::SUCCESSFUL);
        $klarnaQuote->setAuthorizationToken('aaa');
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(0);
        $klarnaQuote->save();

        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('0', $result['body']['is_active']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestWasSuccessfulSinceAuthorizationWorkflowHasTheStatusFailed(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setAuthCallbackActiveCurrentStatus(Quote::FAILED);
        $klarnaQuote->setAuthorizationToken('aaa');
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('1', $result['body']['is_active']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRequestWasSuccessfulSinceAuthorizationWorkflowHasTheStatusNotStarted(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setAuthCallbackActiveCurrentStatus(Quote::NOT_STARTED);
        $klarnaQuote->setAuthorizationToken('aaa');
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $result = $this->sendRequest(
            ['authorization_token' => 'aaa'],
            'checkout/klarna/quoteStatus',
            Http::METHOD_POST
        );

        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('1', $result['body']['is_active']);
    }

}
