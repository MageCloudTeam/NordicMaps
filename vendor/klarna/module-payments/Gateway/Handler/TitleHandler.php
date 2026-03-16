<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Gateway\Handler;

use Klarna\Kp\Api\QuoteRepositoryInterface as KlarnaQuoteRepositoryInterface;
use Klarna\Kp\Model\PaymentMethods\TitleProvider;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Quote\Model\Quote\Payment;

/**
 * @internal
 */
class TitleHandler implements ValueHandlerInterface
{

    /**
     * @var KlarnaQuoteRepositoryInterface
     */
    private $klarnaQuoteRepository;
    /**
     * @var TitleProvider
     */
    private $titleProvider;

    /**
     * @param KlarnaQuoteRepositoryInterface $klarnaQuoteRepository
     * @param TitleProvider $titleProvider
     * @codeCoverageIgnore
     */
    public function __construct(KlarnaQuoteRepositoryInterface $klarnaQuoteRepository, TitleProvider $titleProvider)
    {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->titleProvider = $titleProvider;
    }

    /**
     * Getting back the title of the selected payment method.
     * This does not have a big impact on the frontend store but more in the admin for example on the order page in the
     * Klarna merchant portal box.
     *
     * @param array    $subject
     * @param int|null $storeId
     * @return mixed
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function handle(array $subject, $storeId = null)
    {
        if (!isset($subject['payment'])) {
            return TitleProvider::DEFAULT_TITLE;
        }
        /** @var Payment $payment */
        $payment = $subject['payment']->getPayment();

        $magentoQuote = $payment->getQuote();
        if ($payment->getMethod() && $magentoQuote) {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($magentoQuote);
            return $this->titleProvider->getByKlarnaQuote($klarnaQuote, $payment->getMethod());
        }
        return $this->titleProvider->getByAdditionalInformation($payment);
    }
}
