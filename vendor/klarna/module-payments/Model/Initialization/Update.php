<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Api\Builder\Request;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Update
{
    /**
     * @var CreditApiInterface
     */
    private CreditApiInterface $api;
    /**
     * @var KlarnaQuote
     */
    private KlarnaQuote $klarnaQuote;
    /**
     * @var Startup
     */
    private Startup $startup;
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @param CreditApiInterface $api
     * @param KlarnaQuote $klarnaQuote
     * @param Startup $startup
     * @param Validator $validator
     * @param Request $request
     * @codeCoverageIgnore
     */
    public function __construct(
        CreditApiInterface $api,
        KlarnaQuote $klarnaQuote,
        Startup $startup,
        Validator $validator,
        Request $request
    ) {
        $this->api = $api;
        $this->klarnaQuote = $klarnaQuote;
        $this->startup = $startup;
        $this->validator = $validator;
        $this->request = $request;
    }

    /**
     * Updating the Klarna session
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $quote
     * @return QuoteInterface
     * @throws KlarnaApiException
     * @throws KlarnaException
     */
    public function updateKlarnaSession(QuoteInterface $klarnaQuote, CartInterface $quote): QuoteInterface
    {
        if ($this->validator->isCustomerTypeChanged($klarnaQuote, $quote)) {
            $this->klarnaQuote->markInactiveByKlarnaSessionId($klarnaQuote->getSessionId());
            return $this->startup->createKlarnaSession($quote);
        }

        $klarnaQuote = $this->klarnaQuote->updateKlarnaQuote($quote, $klarnaQuote);
        if (!$klarnaQuote->isActive()) {
            return $this->startup->createKlarnaSession($quote);
        }

        return $klarnaQuote;
    }
}
