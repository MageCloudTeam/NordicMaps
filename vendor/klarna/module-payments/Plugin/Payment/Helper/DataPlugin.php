<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Plugin\Payment\Helper;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Initialization\Action;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use Klarna\Kp\Model\QuoteProvider\QuoteProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;

/**
 * intercepts \Magento\Payment\Helper\Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class DataPlugin
{
    /**
     * @var QuoteProviderInterface
     */
    private $quoteProvider;
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var Action
     */
    private Action $action;

    /**
     * @param QuoteProviderInterface $quoteProvider
     * @param QuoteRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     * @param PaymentMethodProvider $paymentMethodProvider
     * @param ApiValidation $apiValidation
     * @param Action $action
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteProviderInterface $quoteProvider,
        QuoteRepositoryInterface $quoteRepository,
        LoggerInterface $logger,
        PaymentMethodProvider $paymentMethodProvider,
        ApiValidation $apiValidation,
        Action $action
    ) {
        $this->quoteProvider = $quoteProvider;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->apiValidation = $apiValidation;
        $this->action = $action;
    }

    /**
     * Modify results of getPaymentMethods() call to add in Klarna methods returned by API
     *
     * @param Data  $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGetPaymentMethods(Data $subject, array $result)
    {
        $quote = $this->quoteProvider->getQuote();
        if ($quote === null || !$quote->getIsActive()) {
            return $result;
        }

        if (!$this->apiValidation->sendApiRequestAllowed($quote)) {
            return $result;
        }

        if (!$this->isKlarnaOrderUpdateSuccessful($quote)) {
            return $result;
        }

        $klarnaQuote = $this->quoteRepository->getActiveByQuote($quote);
        $methods = $klarnaQuote->getPaymentMethodInfo();
        if (empty($methods)) {
            return $result;
        }

        return $this->addPaymentMethodCodesAndTitlesToResult($methods, $result);
    }

    /**
     * Returns true if the Klarna update order request is successful
     *
     * @param CartInterface $quote
     * @return bool
     */
    private function isKlarnaOrderUpdateSuccessful(CartInterface $quote): bool
    {
        try {
            $this->action->sendRequest($quote);
        } catch (NoSuchEntityException | KlarnaException $e) {
            $this->logger->error($e);

            return false;
        }

        return true;
    }

    /**
     * For every payment method: adds codes and title to the $result
     *
     * @param array $methods
     * @param array $result
     */
    private function addPaymentMethodCodesAndTitlesToResult(array $methods, array $result): array
    {
        foreach ($methods as $method) {
            $code                   = 'klarna_' . $method['identifier'];
            $result[$code]          = $result['klarna_kp'];
            $result[$code]['title'] = $method['identifier'];
        }

        return $result;
    }

    /**
     * Returning the KP instance so that there is a mapping between the Klarna code and the payment instance
     *
     * @param Data     $subject
     * @param callable $proceed
     * @param string   $code
     * @return MethodInterface
     * @throws LocalizedException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function aroundGetMethodInstance(Data $subject, callable $proceed, $code)
    {
        if (false === strpos($code, 'klarna_')) {
            return $proceed($code);
        }
        if ($code === 'klarna_kco') {
            return $proceed($code);
        }

        return $this->paymentMethodProvider->createPaymentMethod($code);
    }
}
