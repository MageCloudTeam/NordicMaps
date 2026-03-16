<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model;

use Klarna\Kp\Model\Initialization\Action;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;
use Klarna\Base\Exception;
use Klarna\Base\Model\Api\Exception as ApiException;

/**
 * @internal
 */
class Payment
{
    /**
     * @var Action
     */
    private Action $action;

    /**
     * @param Action $action
     * @codeCoverageIgnore
     */
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * Sets payment method for quote.
     *
     * @param Quote $quote
     * @return void
     * @throws ApiException
     * @throws Exception
     * @throws LocalizedException
     */
    public function setResponsePaymentMethodToQuote(Quote $quote): void
    {
        $klarnaResponse = $this->action->sendRequest($quote);
        $paymentMethodCategories = $klarnaResponse->getPaymentMethods();

        if (count($paymentMethodCategories) > 0) {
            $methodCode = $paymentMethodCategories[0];
            $quote->setPaymentMethod($methodCode);
            $quote->getPayment()->importData(['method' => $methodCode]);
        }
    }
}
