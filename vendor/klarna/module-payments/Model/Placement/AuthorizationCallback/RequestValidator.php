<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement\AuthorizationCallback;

use Klarna\Kp\Api\QuoteAuthCallbackTokenInterface;
use Magento\Framework\App\RequestContentInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * @internal
 */
class RequestValidator
{
    /**
     * @var RequestContentInterface
     */
    private RequestContentInterface $request;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;

    /**
     * @param RequestContentInterface $request
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(RequestContentInterface $request, CartRepositoryInterface $magentoQuoteRepository)
    {
        $this->request = $request;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
    }

    /**
     * Validate json body, throw an exception on failure
     *
     * @return void
     * @throws LocalizedException
     */
    public function validateRequestBody(): void
    {
        $parameters = json_decode($this->request->getContent(), true);

        foreach (['session_id', 'authorization_token'] as $parameterName) {
            if (empty($parameters[$parameterName])) {
                throw new LocalizedException(__(sprintf('%s is required.', $parameterName)));
            }
        }
    }

    /**
     * Compare the request authorization callback token against the one in KlarnaQuote
     *
     * @param QuoteAuthCallbackTokenInterface $klarnaQuote
     * @return void
     * @throws LocalizedException
     */
    public function verifyAuthCallbackToken(QuoteAuthCallbackTokenInterface $klarnaQuote): void
    {
        if ($this->request->getParam('token') != $klarnaQuote->getAuthTokenCallbackToken()) {
            throw new LocalizedException(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['fieldName' => 'token', 'value' => $this->request->getParam('token')]
                )
            );
        }
    }

    /**
     * Verify Magento quote
     *
     * @param int $quoteId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function verifyMagentoQuote(string $quoteId): void
    {
        $magentoQuote = $this->magentoQuoteRepository->get($quoteId);

        if (!$magentoQuote->getIsActive()) {
            throw new LocalizedException(
                __(sprintf('cartId = %s is not active.', $quoteId))
            );
        }
    }
}
