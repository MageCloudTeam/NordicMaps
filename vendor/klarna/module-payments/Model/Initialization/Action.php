<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Kp\Api\QuoteInterface;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;

/**
 * @internal
 */
class Action
{
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var Update
     */
    private Update $update;
    /**
     * @var Startup
     */
    private Startup $startup;
    /**
     * @var QuoteInterface|null
     */
    private ?QuoteInterface $lastRequestResult = null;

    /**
     * @param Validator $validator
     * @param Update $update
     * @param Startup $startup
     * @codeCoverageIgnore
     */
    public function __construct(Validator $validator, Update $update, Startup $startup)
    {
        $this->validator = $validator;
        $this->update = $update;
        $this->startup = $startup;
    }

    /**
     * Sending the request and returning the response object.
     *
     * If the method was called on a previous step then the result of the last method call will be returned.
     * If a request still ahs to be sent the boolean parameter has to be set to true.
     *
     * @param CartInterface $quote
     * @param bool $forceSendRequest
     * @return QuoteInterface
     * @throws KlarnaException
     * @throws KlarnaApiException
     */
    public function sendRequest(CartInterface $quote, bool $forceSendRequest = false): QuoteInterface
    {
        if ($this->validator->isKlarnaSessionRunning($quote)) {
            if ($this->lastRequestResult !== null && $forceSendRequest === false) {
                return $this->lastRequestResult;
            }

            $klarnaQuote = $this->validator->getKlarnaQuote();
            if ($klarnaQuote->isKecSession()) {
                return $klarnaQuote;
            }
            $this->lastRequestResult = $this->update->updateKlarnaSession($klarnaQuote, $quote);
            return $this->lastRequestResult;
        }

        $this->lastRequestResult = $this->startup->createKlarnaSession($quote);
        return $this->lastRequestResult;
    }
}
