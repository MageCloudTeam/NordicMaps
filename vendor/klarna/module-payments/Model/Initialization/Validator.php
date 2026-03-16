<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Api\Builder\Customer\TypeResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Validator
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $quoteRepository;
    /**
     * @var QuoteInterface
     */
    private QuoteInterface $klarnaQuote;
    /**
     * @var TypeResolver
     */
    private TypeResolver $typeResolver;

    /**
     * @param QuoteRepositoryInterface $quoteRepository
     * @param TypeResolver $typeResolver
     * @codeCoverageIgnore
     */
    public function __construct(QuoteRepositoryInterface $quoteRepository, TypeResolver $typeResolver)
    {
        $this->quoteRepository = $quoteRepository;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Returns true if a Klarna session is already running
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function isKlarnaSessionRunning(CartInterface $quote): bool
    {
        try {
            $this->klarnaQuote = $this->quoteRepository->getActiveByQuote($quote);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        if ($this->klarnaQuote->isKecSession()) {
            return true;
        }

        if (!$this->klarnaQuote->getSessionId()) {
            $this->quoteRepository->markInactive($this->klarnaQuote);
            return false;
        }

        return true;
    }

    /**
     * Returns true if the customer type changed
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $quote
     * @return bool
     */
    public function isCustomerTypeChanged(QuoteInterface $klarnaQuote, CartInterface $quote): bool
    {
        $oldCustomerType = $klarnaQuote->getCustomerType();
        $newCustomerType = $this->typeResolver->getData($quote);

        return $oldCustomerType !== $newCustomerType;
    }

    /**
     * Returning the Klarna quote if any was found in the validation check
     *
     * @return QuoteInterface|null
     */
    public function getKlarnaQuote(): ?QuoteInterface
    {
        return $this->klarnaQuote;
    }
}
