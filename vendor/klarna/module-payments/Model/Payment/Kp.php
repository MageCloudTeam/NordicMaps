<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Payment;

use Klarna\Base\Payment\Core;
use Klarna\Kp\Api\KlarnaPaymentMethodInterface;
use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Phrase;
use Magento\Payment\Model\Method\Adapter;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Klarna Payment
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @internal
 */
class Kp extends Core implements KlarnaPaymentMethodInterface
{
    public const METHOD_CODE = 'klarna_kp';

    public const ONE_KLARNA_PAYMENT_METHOD_CODE = 'klarna';
    public const ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX = 'klarna_' . self::ONE_KLARNA_PAYMENT_METHOD_CODE;

    public const ONE_KLARNA_PAYMENT_METHOD_INFO = [
        [
            'asset_urls' => [
                'descriptive' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg',
                'standard' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg'
            ],
            'identifier' => self::ONE_KLARNA_PAYMENT_METHOD_CODE,
            'name' => ''
        ]
    ];

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var string
     */
    private $code = 'klarna_kp';
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;

    /**
     * @param Adapter $adapter
     * @param Resolver $resolver
     * @param ApiValidation $apiValidation
     * @param StoreManagerInterface $storeManager
     * @param PaymentMethodProvider $paymentMethodProvider
     * @codeCoverageIgnore
     */
    public function __construct(
        Adapter $adapter,
        Resolver $resolver,
        ApiValidation $apiValidation,
        StoreManagerInterface $storeManager,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        parent::__construct($adapter);
        $this->adapter = $adapter;
        $this->resolver = $resolver;
        $this->apiValidation = $apiValidation;
        $this->storeManager = $storeManager;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * @inheritdoc
     */
    public function isActive($storeId = null)
    {
        $store = $this->storeManager->getStore($storeId);
        return $this->apiValidation->isKpEnabled($store);
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $result = $this->adapter->isAvailable($quote);
        if (!$result) {
            return $result;
        }

        if ($this->code === self::ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX) {
            return true;
        }

        return $this->paymentMethodProvider->existMethodInAvailableMethodList($quote, $this->getCode());
    }
}
