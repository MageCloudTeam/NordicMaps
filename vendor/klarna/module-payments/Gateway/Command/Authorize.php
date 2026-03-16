<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Gateway\Command;

use Klarna\Base\Exception as KlarnaBaseException;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Klarna\Kp\Model\Placement\Authorize as PlacementAuthorize;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Authorize implements CommandInterface
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var PlacementAuthorize
     */
    private PlacementAuthorize $placementAuthorize;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;

    /**
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param PlacementAuthorize $placementAuthorize
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteRepositoryInterface $klarnaQuoteRepository,
        PlacementAuthorize $placementAuthorize,
        CartRepositoryInterface $magentoQuoteRepository
    ) {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->placementAuthorize = $placementAuthorize;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
    }

    /**
     * @inheritDoc
     *
     * @throws CommandException
     */
    public function execute(array $commandSubject): ?ResultInterface
    {
        try {
            return $this->authorizePaymentForOrder($commandSubject);
        } catch (\Throwable $throwable) {
            $phrase = __($throwable->getMessage());
            $exception = new \Exception($throwable->getMessage(), $throwable->getCode(), $throwable);

            throw new CommandException($phrase, $exception);
        }
    }

    /**
     * Authorizes the payment for the Order
     *
     * @param array $commandSubject
     * @return ResultInterface|null
     * @throws CouldNotSaveException
     * @throws KlarnaApiException
     * @throws KlarnaBaseException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function authorizePaymentForOrder(array $commandSubject): ?ResultInterface
    {
        /** @var InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        $magentoQuote = $this->magentoQuoteRepository->get($order->getQuoteId());
        $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($magentoQuote);

        $this->placementAuthorize->performAuthorization($klarnaQuote, $magentoQuote, $order, $payment);
        return null;
    }
}
