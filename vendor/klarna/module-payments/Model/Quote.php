<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model;

use Klarna\Kp\Api\AuthorizationCallbackStatusInterface;
use Klarna\Kp\Api\QuoteCustomerTypeInterface;
use Klarna\Kp\Api\QuoteAuthCallbackTokenInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteIsKecSessionInterface;
use Klarna\Kp\Api\QuoteOrderIdInterface;
use Klarna\Kp\Api\QuoteRedirectUrlInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @internal
 */
class Quote extends AbstractModel implements
    QuoteInterface,
    IdentityInterface,
    QuoteOrderIdInterface,
    QuoteRedirectUrlInterface,
    QuoteAuthCallbackTokenInterface,
    QuoteCustomerTypeInterface,
    AuthorizationCallbackStatusInterface,
    QuoteIsKecSessionInterface
{
    public const CACHE_TAG = 'klarna_payments_quote';

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getSessionId()
    {
        return $this->_getData('session_id');
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($active)
    {
        $this->setData('is_active', $active);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setClientToken($token)
    {
        $this->setData('client_token', $token);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getClientToken()
    {
        return $this->_getData('client_token');
    }

    /**
     * @inheritdoc
     */
    public function setSessionId($sessionId)
    {
        $this->setData('session_id', $sessionId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return (bool)$this->_getData('is_active');
    }

    /**
     * @inheritdoc
     */
    public function getAuthorizationToken()
    {
        return $this->_getData('authorization_token');
    }

    /**
     * @inheritdoc
     */
    public function setAuthorizationToken($token)
    {
        $this->setData('authorization_token', $token);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        $this->setData('quote_id', $quoteId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->_getData('quote_id');
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethods()
    {
        $methods = $this->_getData('payment_methods');
        if (empty($methods)) {
            return [];
        }
        return explode(',', $methods);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethods($methods)
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }
        $this->setData('payment_methods', implode(',', $methods));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethodInfo($methodinfo)
    {
        if (!is_array($methodinfo)) {
            $methodinfo = [$methodinfo];
        }
        $this->setData('payment_method_info', json_encode($methodinfo));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethodInfo()
    {
        $methods = $this->_getData('payment_method_info');
        if (empty($methods)) {
            return [];
        }
        return json_decode($methods, true);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId(string $orderId): self
    {
        $this->setData('order_id', $orderId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderId(): ?string
    {
        return $this->_getData('order_id');
    }

    /**
     * @inheritdoc
     */
    public function setRedirectUrl(string $url): self
    {
        $this->setData('redirect_url', $url);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectUrl(): string
    {
        return $this->_getData('redirect_url');
    }

    /**
     * @inheritdoc
     */
    public function setAuthTokenCallbackToken(string $token): QuoteAuthCallbackTokenInterface
    {
        $this->setData('auth_callback_token', $token);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuthTokenCallbackToken(): ?string
    {
        return $this->_getData('auth_callback_token');
    }

    /**
     * @inheritdoc
     */
    public function setCustomerType(string $customerType): self
    {
        $this->setData('customer_type', $customerType);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerType(): ?string
    {
        return $this->_getData('customer_type');
    }

    /**
     * @inheritdoc
     */
    public function setAuthCallbackActiveCurrentStatus(int $status): AuthorizationCallbackStatusInterface
    {
        $this->setData('is_auth_callback_active', $status);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAuthCallbackFailedOrNotStarted(): bool
    {
        return $this->_getData('is_auth_callback_active') <= 0;
    }

    /**
     * @inheritdoc
     */
    public function isAuthCallbackAlreadyProcessed(): bool
    {
        return $this->_getData('is_auth_callback_active') >= 1;
    }

    /**
     * @inheritdoc
     */
    public function isAuthCallbackInProgress(): bool
    {
        return $this->_getData('is_auth_callback_active') == 1;
    }

    /**
     * @inheritdoc
     */
    public function markAsKecSession(): QuoteIsKecSessionInterface
    {
        $this->setData('is_kec_session', true);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isKecSession(): bool
    {
        return (bool) $this->_getData('is_kec_session');
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(\Klarna\Kp\Model\ResourceModel\Quote::class);
    }
}
