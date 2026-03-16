<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Initialization;

/**
 * @internal
 */
class Payload
{
    /**
     * @var QuoteFetcher
     */
    private QuoteFetcher $quoteFetcher;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;

    /**
     * @param QuoteFetcher $quoteFetcher
     * @param RequestBuilder $requestBuilder
     * @codeCoverageIgnore
     */
    public function __construct(QuoteFetcher $quoteFetcher, RequestBuilder $requestBuilder)
    {
        $this->quoteFetcher = $quoteFetcher;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Getting back the payload
     *
     * @param array $parameter
     * @return array
     */
    public function getRequest(array $parameter): array
    {
        $magentoQuote = $this->quoteFetcher->getMagentoQuote($parameter);
        return $this->requestBuilder->getRequest($magentoQuote);
    }
}
