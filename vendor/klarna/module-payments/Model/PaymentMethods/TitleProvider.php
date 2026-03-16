<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Api\QuoteInterface;
use Magento\Payment\Model\InfoInterface;

/**
 * @api
 */
class TitleProvider
{
    public const DEFAULT_TITLE        = 'Klarna Payments';
    public const DEFAULT_TITLE_FORMAT = '%s (%s)';

    /**
     * Getting back the title from the additional_information node
     *
     * @param InfoInterface $payment
     * @return string
     */
    public function getByAdditionalInformation(InfoInterface $payment): string
    {
        if ($payment->getAdditionalInformation('method_title')) {
            return 'Klarna ' .  $payment->getAdditionalInformation('method_title');
        }

        if ($payment->hasAdditionalInformation('method_code')) {
            return sprintf(
                self::DEFAULT_TITLE_FORMAT,
                self::DEFAULT_TITLE,
                $payment->hasAdditionalInformation('method_code')
            );
        }

        return self::DEFAULT_TITLE;
    }

    /**
     * Getting back the title of the selected payment method
     *
     * @param QuoteInterface $klarnaQuote
     * @param string $paymentMethod
     * @return string
     */
    public function getByKlarnaQuote(QuoteInterface $klarnaQuote, string $paymentMethod): string
    {
        $paymentCategories = $klarnaQuote->getPaymentMethodInfo();
        foreach ($paymentCategories as $paymentCategory) {
            if ($this->isTargetPaymentMethod($paymentCategory['identifier'], $paymentMethod)) {
                return $paymentCategory['name'];
            }
        }
        return self::DEFAULT_TITLE;
    }

    /**
     * Returns true if the default title is used
     *
     * @param QuoteInterface $klarnaQuote
     * @param string $paymentMethod
     * @return bool
     * @deprecated No longer used
     * @see \Klarna\Kp\Model\Payment\Kp::isAvailable
     */
    public function isDefaultTitle(QuoteInterface $klarnaQuote, string $paymentMethod): bool
    {
        return $this->getByKlarnaQuote($klarnaQuote, $paymentMethod) === self::DEFAULT_TITLE;
    }

    /**
     * Returns trues if the selected payment method matches the identifier
     *
     * @param string $identifier
     * @param string $paymentMethod
     * @return bool
     */
    private function isTargetPaymentMethod(string $identifier, string $paymentMethod): bool
    {
        return str_replace(' ', '_', $identifier) === str_replace("klarna_", "", $paymentMethod);
    }
}
