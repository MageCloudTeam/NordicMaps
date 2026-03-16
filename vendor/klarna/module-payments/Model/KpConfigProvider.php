<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Helper\ConfigHelper;
use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Configuration\Payment;
use Klarna\Kp\Model\Initialization\Action;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use Klarna\Logger\Model\Configuration\General;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class KpConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigHelper
     */
    private $config;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var PaymentMethodProvider
     */
    private $paymentMethodProvider;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var Action
     */
    private Action $action;
    /**
     * @var Payment
     */
    private Payment $paymentConfiguration;
    /**
     * @var General
     */
    private General $generalConfiguration;

    /**
     * @param ConfigHelper $config
     * @param Session $session
     * @param UrlInterface $urlBuilder
     * @param PaymentMethodProvider $paymentMethodProvider
     * @param ApiValidation $apiValidation
     * @param Action $action
     * @param Payment $paymentConfiguration
     * @param General $generalConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        ConfigHelper $config,
        Session $session,
        UrlInterface $urlBuilder,
        PaymentMethodProvider $paymentMethodProvider,
        ApiValidation $apiValidation,
        Action $action,
        Payment $paymentConfiguration,
        General $generalConfiguration
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->urlBuilder = $urlBuilder;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->apiValidation = $apiValidation;
        $this->action = $action;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->generalConfiguration = $generalConfiguration;
    }

    /**
     * Return payment config for frontend JS to use
     *
     * @return string[][]
     */
    public function getConfig()
    {
        $store = $this->session->getQuote()->getStore();
        $paymentConfig = [
            'payment' => [
                'klarna_kp' => [
                    'client_token'        => null,
                    'message'             => null,
                    'success'             => 0,
                    'debug'               => $this->generalConfiguration->isDebuggingEnabled($store),
                    'enabled'             => $this->apiValidation->isKpEnabled($store),
                    'b2b_enabled'         => $this->paymentConfiguration->isB2bEnabled($store),
                    'data_sharing_onload' => $this->paymentConfiguration->isDataSharingOnLoadEnabled($store),
                    'available_methods'   => [
                        'type'      => 'klarna_kp',
                        'component' => 'Klarna_Kp/js/view/payments/kp'
                    ],
                    'reload_checkout_config_url' => $this->urlBuilder->getUrl('checkout/klarna/checkoutConfig'),
                    'redirect_url' => $this->urlBuilder->getUrl('checkout/klarna/cookie'),
                    'update_session_url' => $this->urlBuilder->getUrl('checkout/klarna/updateSession'),
                    'get_quote_status_url' => $this->urlBuilder->getUrl('checkout/klarna/quoteStatus'),
                    'authorization_token_update_url'
                        => $this->urlBuilder->getUrl('checkout/klarna/authorizationTokenUpdate'),
                    'update_quote_email_url' => $this->urlBuilder->getUrl('checkout/klarna/updateQuoteEmail')
                ]
            ]
        ];

        $this->apiValidation->clearFailedValidationHistory();
        if (!$this->apiValidation->sendApiRequestAllowed($this->session->getQuote())) {
            $paymentConfig['payment']['klarna_kp']['message'] =
                __('Klarna Payments will not show up. Reason: ' .
                    implode(', ', $this->apiValidation->getFailedValidationHistory()));
            return $paymentConfig;
        }
        try {
            $klarnaQuote = $this->action->sendRequest($this->session->getQuote());
            $paymentConfig['payment']['klarna_kp']['client_token'] = $klarnaQuote->getClientToken();
            $paymentConfig['payment']['klarna_kp']['authorization_token'] = $klarnaQuote->getAuthorizationToken();
            $paymentConfig['payment']['klarna_kp']['success'] = 1;
            $paymentConfig['payment']['klarna_kp']['is_kec_session'] = $klarnaQuote->isKecSession();

            $methods = $klarnaQuote->getPaymentMethodInfo();
            $paymentConfig = $this->paymentMethodProvider->getAvailablePaymentMethods($methods, $paymentConfig);
        } catch (KlarnaException $e) {
            $paymentConfig['payment']['klarna_kp']['message'] = $e->getMessage();
        }
        return $paymentConfig;
    }
}
