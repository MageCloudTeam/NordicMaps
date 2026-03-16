<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * @api
 */
interface ResponseInterface
{
    /**
     * Getting back the session id
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Getting back the client token
     *
     * @return string
     */
    public function getClientToken();

    /**
     * Getting back the response code
     *
     * @return int
     */
    public function getResponseCode();

    /**
     * Getting back the payment method categories.
     *
     * @return array
     */
    public function getPaymentMethodCategories();

    /**
     * Getting back the message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Getting back the order id.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Getting back the redirect url.
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Getting back the fraud status.
     *
     * @return int
     */
    public function getFraudStatus();

    /**
     * Returning the isSuccessful flag.
     *
     * @return bool
     */
    public function isSuccessfull();

    /**
     * Converting to array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns true if the session is expired
     *
     * @return bool
     */
    public function isExpired(): bool;
}
