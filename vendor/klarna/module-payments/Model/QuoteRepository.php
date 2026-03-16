<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model;

use Klarna\Kp\Api\QuoteAuthorizationTokenRepositoryInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * @internal
 */
class QuoteRepository implements QuoteRepositoryInterface, QuoteAuthorizationTokenRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteResource
     */
    private $resourceModel;

    /**
     * Holds a cache of instances to avoid unnecessary db and API calls
     *
     * @var array
     */
    private $instancesById = [];

    /**
     * Holds a cache of instances to avoid unnecessary db and API calls
     *
     * @var array
     */
    private $instances = [];

    /**
     * @param QuoteFactory $quoteFactory
     * @param QuoteResource $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        QuoteResource $resourceModel
    ) {
        $this->quoteFactory  = $quoteFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Get quote by Magento quote
     *
     * @param MageQuoteInterface $mageQuote
     * @return QuoteInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     *
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote)
    {
        $paymentsQuoteId = $this->resourceModel->getActiveByQuote($mageQuote);
        if (!$paymentsQuoteId) {
            throw NoSuchEntityException::singleField('quote_id', $mageQuote->getId());
        }
        return $this->loadQuote('load', 'payments_quote_id', $paymentsQuoteId);
    }

    /**
     * @inheritDoc
     */
    public function getActiveByQuoteId(string $mageQuoteId): QuoteInterface
    {
        $quoteId = $this->resourceModel->getActiveByQuoteId($mageQuoteId);
        if (!$quoteId) {
            throw NoSuchEntityException::singleField('quote_id', $mageQuoteId);
        }
        return $this->loadQuote('load', 'payments_quote_id', $quoteId);
    }

    /**
     * Load quote with different methods
     *
     * @param string $loadMethod
     * @param string $loadField
     * @param int    $identifier
     * @return QuoteInterface
     *
     * @SuppressWarnings(PMD.StaticAccess)
     * @throws NoSuchEntityException
     */
    private function loadQuote($loadMethod, $loadField, $identifier)
    {
        /** @var QuoteInterface $quote */
        $quote = $this->quoteFactory->create();
        $quote->$loadMethod($identifier, $loadField);
        if (!$quote->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }

    /**
     * Getting back a empty instance
     *
     * @return QuoteInterface
     */
    public function getEmptyInstance(): QuoteInterface
    {
        return $this->quoteFactory->create();
    }

    /**
     * Delete quote by ID
     *
     * @param int $id
     * @return void
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        $this->delete($this->getById($id));
    }

    /**
     * Delete quote
     *
     * @param QuoteInterface $quote
     * @return void
     * @throws \Exception
     */
    public function delete(QuoteInterface $quote)
    {
        $quoteId   = $quote->getId();
        $sessionId = $quote->getSessionId();

        $this->resourceModel->delete($quote);
        unset($this->instances[$sessionId]);
        unset($this->instancesById[$quoteId]);
    }

    /**
     * Get quote by ID
     *
     * @param int  $quoteId
     * @param bool $forceReload
     * @return QuoteInterface
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function getById($quoteId, $forceReload = false)
    {
        if (!isset($this->instancesById[$quoteId]) || $forceReload) {
            /** @var QuoteInterface $quote */
            $quote = $this->loadQuote('load', 'payments_quote_id', $quoteId);
            $this->cacheInstance($quote);
        }
        return $this->instancesById[$quoteId];
    }

    /**
     * Cache instance locally in memory to avoid additional DB calls
     *
     * @param QuoteInterface $quote
     */
    private function cacheInstance(QuoteInterface $quote)
    {
        $this->instancesById[$quote->getId()]    = $quote;
        $this->instances[$quote->getSessionId()] = $quote;
    }

    /**
     * Mark quote as inactive and cancel it with API
     *
     * @param QuoteInterface $quote
     * @throws CouldNotSaveException
     */
    public function markInactive(QuoteInterface $quote)
    {
        $quote->setIsActive(0);
        $this->save($quote);
    }

    /**
     * Save Klarna Quote
     *
     * @param QuoteInterface $quote
     * @return QuoteResource
     * @throws CouldNotSaveException
     */
    public function save(QuoteInterface $quote)
    {
        try {
            return $this->resourceModel->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * Load quote by session_id
     *
     * @param string $sessionId
     * @param bool   $forceReload
     * @return QuoteInterface
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function getBySessionId($sessionId, $forceReload = false)
    {
        if ($forceReload || !isset($this->instances[$sessionId])) {
            $quote = $this->loadQuote('load', 'session_id', $sessionId);
            $this->cacheInstance($quote);
        }
        return $this->instances[$sessionId];
    }

    /**
     * Getting back the authorization token
     *
     * @param string $authorizationToken
     * @return QuoteInterface
     * @throws NoSuchEntityException
     */
    public function getByAuthorizationToken(string $authorizationToken): QuoteInterface
    {
        $quote = $this->loadQuote('load', 'authorization_token', $authorizationToken);
        $this->cacheInstance($quote);

        return $quote;
    }

    /**
     * Returns true if the session id is already saved in the database
     *
     * @param string $sessionId
     * @return bool
     */
    public function existSessionIdEntry(string $sessionId): bool
    {
        /** @var QuoteInterface $quote */
        $quote = $this->quoteFactory->create();
        $quote->load($sessionId, 'session_id');

        if ($quote->getId() !== null) {
            $this->cacheInstance($quote);
            return true;
        }

        return false;
    }
}
