<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Framework\Math\Random;
use Magento\Framework\Url;

/**
 * @internal
 */
class MerchantUrls
{
    /**
     * @var Url
     */
    private Url $url;

    /**
     * @param Url $url
     * @codeCoverageIgnore
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * Adding the merchant urls to the request
     *
     * @param Builder $requestBuilder
     * @param string $authCallbackToken
     */
    public function addToRequest(Builder $requestBuilder, string $authCallbackToken): void
    {
        $urlParams = [
            '_nosid'         => true,
            '_forced_secure' => true
        ];

        $urls = [
            'confirmation' => $this->url->getDirectUrl('checkout/onepage/success', $urlParams),
            'notification' => preg_replace(
                '/\/id\/{checkout\.order\.id}/',
                '',
                $this->url->getDirectUrl(
                    'klarna/api/disabled',
                    $urlParams
                )
            ),
            'authorization' => $this->url->getDirectUrl(
                'checkout/klarna/authorize?token=' . $authCallbackToken,
                $urlParams
            )
        ];

        $requestBuilder->setMerchantUrls($urls);
    }
}
