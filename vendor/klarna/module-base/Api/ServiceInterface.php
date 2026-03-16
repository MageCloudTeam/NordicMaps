<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Api;

/**
 * @api
 */
interface ServiceInterface
{
    public const POST   = 'post';
    public const GET    = 'get';
    public const PUT    = 'put';
    public const PATCH  = 'patch';
    public const DELETE = 'delete';

    public const SERVICE_KCO = 'Klarna Checkout';
    public const SERVICE_KP = 'Klarna Payments';
    public const SERVICE_OM = 'Klarna Ordermanagement';

    /**
     * Make API call
     *
     * @param string $url
     * @param string $service
     * @param string $body
     * @param string $method HTTP request type
     * @param string $klarnaId
     * @param string $action
     * @return array Response body from API call
     */
    public function makeRequest(
        $url,
        string $service,
        $body = '',
        $method = self::POST,
        $klarnaId = null,
        $action = null
    ): array;

    /**
     * Connect to API
     *
     * @param string $username
     * @param string $password
     * @param string $connectUrl
     * @return bool Whether connect succeeded or not
     */
    public function connect($username, $password, $connectUrl = null): bool;

    /**
     * Setting the user agent.
     *
     * @param string $product
     * @param string $version
     * @param string $mageInfo
     * @return void
     */
    public function setUserAgent($product, $version, $mageInfo): void;

    /**
     * Setting the header.
     *
     * @param string      $header
     * @param string|null $value
     * @return void
     */
    public function setHeader($header, $value = null): void;
}
