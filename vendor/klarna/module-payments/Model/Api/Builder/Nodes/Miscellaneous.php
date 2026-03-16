<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Miscellaneous
{
    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper;

    /**
     * @param MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper
     * @codeCoverageIgnore
     */
    public function __construct(MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper)
    {
        $this->magentoToKlarnaLocaleMapper = $magentoToKlarnaLocaleMapper;
    }

    /**
     * Adding miscellaneous nodes to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        $requestBuilder->setPurchaseCurrency($magentoQuote->getBaseCurrencyCode());
        $requestBuilder->setLocale($this->magentoToKlarnaLocaleMapper->getLocale());
    }
}
