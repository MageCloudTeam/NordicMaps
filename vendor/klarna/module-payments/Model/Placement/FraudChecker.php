<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Kp\Api\Data\ResponseInterface;
use Magento\Payment\Model\InfoInterface;

/**
 * @internal
 */
class FraudChecker
{
    public const FRAUD_STATUS_REJECTED = 'REJECTED';
    public const FRAUD_STATUS_PENDING  = 'PENDING';

    /**
     * Checking the fraud on the API response and setting based on the status the order fraud status
     *
     * @param ResponseInterface $response
     * @param InfoInterface $payment
     */
    public function checkFraud(ResponseInterface $response, InfoInterface $payment): void
    {
        switch ($response->getFraudStatus()) {
            case self::FRAUD_STATUS_REJECTED:
                $payment->setIsFraudDetected(true);
                break;
            case self::FRAUD_STATUS_PENDING:
                $payment->setIsTransactionPending(true);
                break;
            default:
                $payment->setIsTransactionPending(false);
        }
    }
}
