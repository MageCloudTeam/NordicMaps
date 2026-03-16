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
use Klarna\Kp\Api\AuthorizationCallbackStatusInterface;
use Klarna\Kp\Model\Quote;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;

/**
 * @internal
 */
class AuthorizeTest extends ControllerTestCase
{
    /**
     * @var Quote
     */
    private $klarnaQuote;
    /**
     * @var ResourceConnection
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->klarnaQuote = $this->objectManager->get(Quote::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class);
    }

    public function testExecuteDryRunParameterWithValueIsGiven(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?dryRun=1',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('The checkout/klarna/authorize?dryRun=1 is accessible.', $result['body']['message']);
        static::assertTrue(isset($result['body']['timestamp']));
        static::assertEquals(200, $result['body']['code']);
    }

    public function testExecuteMissingSessionIdParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['authorization_token' => '1'],
            'checkout/klarna/authorize?token=2',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('session_id is required.', $result['body']['error']);
    }

    public function testExecuteMissingAuthorizationTokenParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $result = $this->sendRequest(
            ['session_id' => '1'],
            'checkout/klarna/authorize?token=2',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('authorization_token is required.', $result['body']['error']);
    }

    public function testExecuteMissingTokenParameter(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('Invalid value of "" provided for the token field.', $result['body']['error']);
    }

    public function testExecuteNoSessionIdEntryGivenInDatabase(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('No such entity with session_id = 1', $result['body']['error']);
    }

    public function testExecuteValidSessionIdAndTokenButAuthCallbackWorkflowStillRunning(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::IN_PROGRESS);
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?token=my_token',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('Another authorization callback workflow is still in progress.', $result['body']['error']);
    }

    public function testExecutePlacingOrderFailed(): void
    {
        $numberOfOrdersBefore = $this->getNumberMagentoOrders();

        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $this->klarnaQuote->setQuoteId($magentoQuote->getId());
        $this->klarnaQuote->setSessionId('1');
        $this->klarnaQuote->setAuthTokenCallbackToken('my_token');
        $this->klarnaQuote->save();

        $result = $this->sendRequest(
            ['session_id' => '1', 'authorization_token' => '1'],
            'checkout/klarna/authorize?token=my_token',
            Http::METHOD_POST
        );

        $numberOfOrdersAfter = $this->getNumberMagentoOrders();

        static::assertEquals($numberOfOrdersBefore, $numberOfOrdersAfter);
        static::assertEquals(400, $result['statusCode']);
        static::assertEquals('A server error stopped your order from being placed. Please try to place your order again.', $result['body']['error']);
    }

    private function getNumberMagentoOrders(): int
    {
        $query = "SELECT COUNT(*) as number_orders FROM sales_order";
        $connection = $this->connection->getConnection();
        $result = $connection->fetchAll($query);
        return (int) $result[0]['number_orders'];
    }

}
