<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\Factory as PaymentFactory;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class PaymentMethodProvider
{
    /**
     * @var PaymentFactory
     */
    private PaymentFactory $methodFactory;
    /**
     * @var array
     */
    private array $paymentMethods = [];
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;

    /**
     * @param PaymentFactory $methodFactory
     * @param null|QuoteRepositoryInterface $klarnaQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(PaymentFactory $methodFactory, QuoteRepositoryInterface $klarnaQuoteRepository = null)
    {
        $this->methodFactory = $methodFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository ?: ObjectManager::getInstance()->get(
            QuoteRepository::class
        );
    }

    /**
     * Creating the Payment method instance
     *
     * @param string $method
     * @return Kp
     * @throws LocalizedException
     */
    public function createPaymentMethod(string $method): MethodInterface
    {
        if (!isset($this->paymentMethods[$method])) {
            $instance = $this->methodFactory->create(Kp::class);
            $instance->setCode($method);
            $this->paymentMethods[$method] = $instance;

        }
        return $this->paymentMethods[$method];
    }

    /**
     * Extracting the payment methods from the API response and converting them to a string
     *
     * @param ResponseInterface $klarnaResponse
     * @return string
     */
    public function extractByApiResponse(ResponseInterface $klarnaResponse): string
    {
        $payment_methods = [];
        foreach ($klarnaResponse->getPaymentMethodCategories() as $category) {
            $payment_methods[] = 'klarna_' . $category['identifier'];
        }
        return implode(',', $payment_methods);
    }

    /**
     * Getting back the available payment methods
     *
     * @param array $paymentMethodData
     * @param array $paymentMethodConfig
     * @return array
     */
    public function getAvailablePaymentMethods(array $paymentMethodData, array $paymentMethodConfig): array
    {
        $available_methods = [];
        foreach ($paymentMethodData as $method) {
            $identifier = $method['identifier'];
            $available_methods[] = [
                'type'      => 'klarna_' . $identifier,
                'component' => 'Klarna_Kp/js/view/payments/kp'
            ];

            $paymentMethodConfig['payment']['klarna_' . $identifier] = $paymentMethodConfig['payment']['klarna_kp'];
            $paymentMethodConfig['payment']['klarna_' . $identifier]['title'] = $method['name'];
            $paymentMethodConfig['payment']['klarna_' . $identifier]['logo'] = $method['asset_urls']['standard'];
            $paymentMethodConfig['payment']['klarna_kp']['klarna_' . $identifier] =
                $paymentMethodConfig['payment']['klarna_' . $identifier];
        }
        $paymentMethodConfig['payment']['klarna_kp']['available_methods'] = $available_methods;
        return $paymentMethodConfig;
    }

    /**
     * Returns true if the given method is part of the available payment method list
     *
     * @param CartInterface $quote
     * @param string $paymentMethod
     * @return bool
     */
    public function existMethodInAvailableMethodList(CartInterface $quote, string $paymentMethod): bool
    {
        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($quote);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        foreach ($klarnaQuote->getPaymentMethodInfo() as $paymentCategory) {
            if ($this->isTargetPaymentMethod($paymentCategory['identifier'], $paymentMethod)) {
                return true;
            }
        }

        return false;
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
