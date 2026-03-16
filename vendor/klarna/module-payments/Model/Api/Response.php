<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api;

use Klarna\Kp\Api\Data\ResponseInterface;

/**
 * @internal
 */
class Response implements ResponseInterface
{
    use Export;

    public const RESPONSE_STATUS_NOT_FOUND = 404;

    /**
     * @var string
     */
    private $session_id;
    /**
     * @var string
     */
    private $client_token;
    /**
     * @var int
     */
    private $fraud_status;
    /**
     * @var string
     */
    private $redirect_url;
    /**
     * @var string
     */
    private $order_id;
    /**
     * @var int
     */
    private $response_code = 418;
    /**
     * @var  string
     */
    private $message;
    /**
     * @var  string
     */
    private $response_status_message;
    /**
     * @var array
     */
    private $payment_method_categories = [];
    /**
     * @var array
     */
    private array $attachment;
    /**
     * @var array
     */
    private array $customer;
    /**
     * @var array
     */
    private array $billing_address;
    /**
     * @var array
     */
    private array $shipping_address = [];

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * Get client token
     *
     * @return string
     */
    public function getClientToken()
    {
        return $this->client_token;
    }

    /**
     * Getting back the response code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * Getting back the order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Getting back the redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     * Getting back the payment method categories
     *
     * @return array
     */
    public function getPaymentMethodCategories()
    {
        return $this->payment_method_categories;
    }

    /**
     * Getting back the fraud status
     *
     * @return int
     */
    public function getFraudStatus()
    {
        return $this->fraud_status;
    }

    /**
     * Returns true if the request was successful
     *
     * @return bool
     */
    public function isSuccessfull()
    {
        return in_array($this->response_code, [200, 201, 204], false);
    }

    /**
     * Getting back the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Getting back the response status message
     *
     * @return string
     */
    public function getResponseStatusMessage(): string
    {
        return $this->response_status_message;
    }

    /**
     * Returns true if the session is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->response_code === self::RESPONSE_STATUS_NOT_FOUND;
    }

    /**
     * Getting back the attachment
     *
     * @return array
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    /**
     * Getting back the customer
     *
     * @return array
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * Getting back the billing address
     *
     * @return array
     */
    public function getBillingAddress(): array
    {
        return $this->billing_address;
    }

    /**
     * Getting back the shipping address
     *
     * @return array
     */
    public function getShippingAddress(): array
    {
        return $this->shipping_address;
    }
}
