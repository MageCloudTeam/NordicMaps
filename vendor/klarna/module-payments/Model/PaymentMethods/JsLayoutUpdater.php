<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Initialization\Action;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Checkout\Model\Session;
use Klarna\Base\Exception as KlarnaException;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class JsLayoutUpdater
{
    /**
     * @var Action
     */
    private Action $action;
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Action $action
     * @param Session $session
     * @param ApiValidation $apiValidation
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        Action $action,
        Session $session,
        ApiValidation $apiValidation,
        LoggerInterface $logger
    ) {
        $this->action = $action;
        $this->session = $session;
        $this->apiValidation = $apiValidation;
        $this->logger = $logger;
    }

    /**
     * Update Klarna available methods
     *
     * @param array $configuration
     * @return array
     */
    public function updateMethods(array $configuration): array
    {
        $quote = $this->session->getQuote();

        try {
            if (!$this->apiValidation->sendApiRequestAllowed($quote)) {
                return $configuration;
            }

            $klarnaQuote = $this->action->sendRequest($quote);
            $paymentMethodInfo = $klarnaQuote->getPaymentMethodInfo();
            if (empty($paymentMethodInfo)) {
                $this->logger->forceLogging(
                    'Can not update the JS layout with Klarna information because ' .
                    'there are no payment method information for the Klarna session ID ' .
                    $klarnaQuote->getSessionId() . ' and Magento quote ID ' . $klarnaQuote->getQuoteId()
                );
                return $configuration;
            }

            foreach ($klarnaQuote->getPaymentMethodInfo() as $method) {
                $configuration['klarna']['methods']['klarna_' . $method['identifier']] =
                    $configuration['klarna']['methods']['klarna_kp'];
            }
        } catch (KlarnaException $e) {
            $this->logger->forceLogging(
                'Failed to update the JS layout for the Magento quote ID ' . $quote->getId() .
                '. Error message: ' . $e->getMessage()
            );
            return $configuration;
        }

        return $configuration;
    }
}
