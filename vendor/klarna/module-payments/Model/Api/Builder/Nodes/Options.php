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
use Klarna\Kp\Model\Configuration\Payment;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Options
{
    /**
     * @var Payment
     */
    private Payment $paymentConfiguration;

    /**
     * @param Payment $paymentConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(Payment $paymentConfiguration)
    {
        $this->paymentConfiguration = $paymentConfiguration;
    }

    /**
     * Adding the options to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        $options = $this->paymentConfiguration->getDesign($magentoQuote->getStore());
        $requestBuilder->setOptions($options);
    }
}
