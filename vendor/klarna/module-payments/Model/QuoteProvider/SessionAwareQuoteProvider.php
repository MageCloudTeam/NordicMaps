<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\QuoteProvider;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class SessionAwareQuoteProvider implements QuoteProviderInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     * @codeCoverageIgnore
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function getQuote(): ?CartInterface
    {
        try {
            return $this->session->getQuote();
        } catch (NoSuchEntityException | LocalizedException $exception) {
            return null;
        }
    }
}
