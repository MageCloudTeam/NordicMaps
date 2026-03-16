<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Klarna\Base\Exception;
use Klarna\Base\Model\Api\Exception as ApiException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class CallbackHandler implements CallbackHandlerInterface
{
    /**
     * @var RegionLoader
     */
    private RegionLoader $regionLoader;
    /**
     * @var Payment
     */
    private Payment $payment;
    /**
     * @var QuoteUpdater
     */
    private QuoteUpdater $quoteUpdater;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;

    /**
     * @param RegionLoader $regionLoader
     * @param Payment $payment
     * @param QuoteUpdater $quoteUpdater
     * @param ApiValidation $apiValidation
     * @codeCoverageIgnore
     */
    public function __construct(
        RegionLoader $regionLoader,
        Payment $payment,
        QuoteUpdater $quoteUpdater,
        ApiValidation $apiValidation
    ) {
        $this->regionLoader = $regionLoader;
        $this->payment = $payment;
        $this->quoteUpdater = $quoteUpdater;
        $this->apiValidation = $apiValidation;
    }

    /**
     * The address callback data will be used to have a quote fully prepared for the order place.
     *
     * @param CartInterface $quote
     * @param array $addressData
     * @return string
     * @throws ApiException
     * @throws Exception
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function handle(CartInterface $quote, array $addressData): string
    {
        $addressDataWithRegion = $this->regionLoader->addRegionToArray($addressData);
        $this->quoteUpdater->updateQuoteByAddressData($quote, $addressDataWithRegion);

        if (!$this->apiValidation->sendApiRequestAllowed($quote)) {
            return '';
        }

        $this->payment->setResponsePaymentMethodToQuote($quote);
        return $quote->getPayment()->getMethod() ?: '';
    }
}
