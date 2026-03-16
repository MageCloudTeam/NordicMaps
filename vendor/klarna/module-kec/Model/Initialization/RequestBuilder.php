<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Initialization;

use Klarna\Base\Api\BuilderInterface;
use Klarna\Kp\Model\Api\Builder\Request;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class RequestBuilder
{
    /**
     * @var Request
     */
    private Request $builder;

    /**
     * @param Request $builder
     * @codeCoverageIgnore
     */
    public function __construct(Request $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Getting back the request
     *
     * @param CartInterface $magentoQuote
     * @return array
     */
    public function getRequest(CartInterface $magentoQuote): array
    {
        $result = $this->builder->generateCreateSessionRequest($magentoQuote, '')
            ->toArray();

        unset($result['merchant_urls']['authorization']);
        $result['intent'] = 'buy';

        return $result;
    }
}
