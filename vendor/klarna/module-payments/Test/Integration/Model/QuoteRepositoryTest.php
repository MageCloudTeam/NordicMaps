<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Model;

use Klarna\Kp\Model\QuoteRepository;
use Klarna\Kp\Model\ResourceModel\Quote as KpQuote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Checkout\Model\Session as MagentoSession;
use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @internal
 */
class QuoteRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MagentoSession
     */
    private $session;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var QuoteRepository
     */
    private $klarnaQuoteRepository;
    /**
     * @var mixed KpQuote
     */
    private $kpQuote;
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var Quote
     */
    private $quote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->session = $this->objectManager->get(MagentoSession::class);
        $this->kpQuote = $this->objectManager->get(KpQuote::class);
        $this->klarnaQuoteRepository = $this->objectManager->get(QuoteRepository::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class);

        $this->quote = $this->session->getQuote();
        $this->quote->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteJustOneEntryExistsAndItsMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteJustOneEntryExistsButItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteSeveralEntriesExistsAndAllMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('_a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteSeveralEntriesExistsButLastEntryIsMarkedAsInactive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('_a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdJustOneEntryExistsAndItsMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdJustOneEntryExistsButItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdSeveralEntriesExistsAndAllMarkedAsActive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('_a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetActiveByQuoteIdSeveralEntriesExistsButLastEntryIsMarkedAsInactive(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('_a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdEntryFoundAndItsMarkedAsInactive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteByIdEntryFoundAndItsMarkedAsActive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);
        $this->klarnaQuoteRepository->getActiveByQuoteId($this->quote->getId());

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        static::assertEquals($this->quote->getId(), $result[0]['quote_id']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testDeleteEntryFoundAndItsMarkedAsActive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($this->quote);
        $this->klarnaQuoteRepository->delete($klarnaQuote);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        static::assertEmpty($result);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getById('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByIdEntryFound(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById($result[0]['payments_quote_id']);

        static::assertEquals($this->quote->getId(), $klarnaQuote->getQuoteId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testMarkInactiveEntryStatusIsChangedToInactive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById($result[0]['payments_quote_id']);

        $this->klarnaQuoteRepository->markInactive($klarnaQuote);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        static::assertEquals(0, $result[0]['is_active']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testMarkInactiveEntryStatusWasAlreadyInactive(): void
    {
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById($result[0]['payments_quote_id']);

        $this->klarnaQuoteRepository->markInactive($klarnaQuote);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        static::assertEquals(0, $result[0]['is_active']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSaveEntryIsSaved(): void
    {
        $expected = 'my_new_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', 'c', 0, " . $this->quote->getId() . ")";
        $connection->query($query);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        $klarnaQuote = $this->klarnaQuoteRepository->getById($result[0]['payments_quote_id']);
        $klarnaQuote->setSessionId($expected);

        $this->klarnaQuoteRepository->save($klarnaQuote);

        $query = "SELECT * FROM klarna_payments_quote where quote_id = " . $this->quote->getId();
        $result = $connection->fetchAll($query);
        static::assertEquals($expected, $result[0]['session_id']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetBySessionIdNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getBySessionId('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetBySessionIdEntryFound(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('" . $expected . "', 'b', 'c', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getBySessionId($expected);
        static::assertEquals($expected, $result->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByAuthorizationTokenNoEntryFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->klarnaQuoteRepository->getByAuthorizationToken('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByAuthorizationTokenEntryFound(): void
    {
        $expected = 'my_target_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_payments_quote(session_id, client_token, authorization_token, is_active, quote_id) VALUES " .
            "('a', 'b', '" . $expected . "', 1, " . $this->quote->getId() . ")";
        $connection->query($query);

        $result = $this->klarnaQuoteRepository->getByAuthorizationToken($expected);
        static::assertEquals($expected, $result->getAuthorizationToken());
    }
}
