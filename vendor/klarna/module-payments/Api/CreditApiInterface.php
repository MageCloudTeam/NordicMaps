<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;

/**
 * @api
 */
interface CreditApiInterface
{

    public const ACTIONS = [
        'cancel_order'           => 'Cancel order',
        'create_order'           => 'Create Order',
        'create_session'         => 'Create session',
        'update_session'         => 'Update session',
        'read_session'           => 'Read session',
        'authorize_callback'     => 'Authorize Callback'
    ];

    /**
     * Creating the session
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function createSession(RequestInterface $request);

    /**
     * Updating the session
     *
     * @param string           $sessionId
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function updateSession(string $sessionId, RequestInterface $request);

    /**
     * Reading the session
     *
     * @param string           $sessionId
     * @return ResponseInterface
     */
    public function readSession(string $sessionId);

    /**
     * Placing the order
     *
     * @param string           $authorizationToken
     * @param RequestInterface $request
     * @param null|string      $klarnaId
     * @param null|string      $incrementId
     * @return ResponseInterface
     */
    public function placeOrder(
        string $authorizationToken,
        RequestInterface $request,
        $klarnaId = null,
        string $incrementId = null
    );

    /**
     * Cancelling the order
     *
     * @param string $authorizationToken
     * @param null   $klarnaId
     * @return ResponseInterface
     */
    public function cancelOrder(string $authorizationToken, $klarnaId = null);
}
