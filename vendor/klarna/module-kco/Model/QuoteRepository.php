<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model;

use Klarna\Kco\Api\QuoteInterface;
use Klarna\Kco\Api\QuoteRepositoryInterface;
use Klarna\Kco\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * @internal
 */
class QuoteRepository implements QuoteRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;
    /**
     * @var QuoteResource
     */
    private QuoteResource $resourceModel;

    /**
     * QuoteRepository constructor.
     *
     * @param QuoteFactory  $quoteFactory
     * @param QuoteResource $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        QuoteResource $resourceModel
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function getByCheckoutId($checkoutId, $forceReload = false): QuoteInterface
    {
        $quoteId = $this->resourceModel->getIdByCheckoutId($checkoutId);
        if (!$quoteId) {
            throw NoSuchEntityException::singleField('quote_id', $checkoutId);
        }
        return $this->loadQuote('kco_quote_id', $quoteId);
    }

    /**
     * Load quote with different methods
     *
     * @param string $loadField
     * @param string $identifier
     * @throws NoSuchEntityException
     * @return QuoteInterface
     */
    private function loadQuote(string $loadField, string $identifier): QuoteInterface
    {
        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $this->resourceModel->load($quote, $identifier, $loadField);
        if (!$quote->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }

    /**
     * @inheritdoc
     */
    public function save(QuoteInterface $quote): QuoteResource
    {
        try {
            return $this->resourceModel->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote): QuoteInterface
    {
        $quoteId = $this->resourceModel->getActiveByQuote($mageQuote);
        if (!$quoteId) {
            throw NoSuchEntityException::singleField('quote_id', $mageQuote->getId());
        }
        return $this->loadQuote('kco_quote_id', $quoteId);
    }
}
