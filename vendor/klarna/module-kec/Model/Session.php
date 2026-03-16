<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model;

use Magento\Customer\Model\Session as CustomerSession;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Session
{
    public const SESSION_KEY_OLD_MAGENTO_QUOTE_ID = 'klarna_kec_custom_old_quote_id';
    public const SESSION_KEY_NEW_MAGENTO_QUOTE_ID = 'klarna_kec_custom_new_quote_id';

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @param CustomerSession $customerSession
     * @codeCoverageIgnore
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Dropping the entry
     */
    public function drop(): void
    {
        if ($this->entryExists()) {
            $customData = $this->customerSession->getCustomData();
            unset(
                $customData[self::SESSION_KEY_OLD_MAGENTO_QUOTE_ID],
                $customData[self::SESSION_KEY_NEW_MAGENTO_QUOTE_ID]
            );

            $this->customerSession->setCustomData($customData);
        }
    }

    /**
     * Saving the quote IDs
     *
     * @param string $newQuoteId
     * @param string|null $oldQuoteId
     */
    public function saveQuoteIds(string $newQuoteId, ?string $oldQuoteId): void
    {
        $customData = $this->customerSession->getCustomData();
        $customData[self::SESSION_KEY_OLD_MAGENTO_QUOTE_ID] = $oldQuoteId;
        $customData[self::SESSION_KEY_NEW_MAGENTO_QUOTE_ID] = $newQuoteId;

        $this->customerSession->setCustomData($customData);
    }

    /**
     * Returns true if the entry exist
     *
     * @return bool
     */
    public function entryExists(): bool
    {
        $customData = $this->customerSession->getCustomData();
        if ($customData === null) {
            return false;
        }

        return array_key_exists(self::SESSION_KEY_OLD_MAGENTO_QUOTE_ID, $customData) &&
            array_key_exists(self::SESSION_KEY_NEW_MAGENTO_QUOTE_ID, $customData);
    }

    /**
     * Getting back the entry
     *
     * @return null|string
     */
    public function getNewQuoteId(): ?string
    {
        if ($this->entryExists()) {
            $customData = $this->customerSession->getCustomData();
            return $customData[self::SESSION_KEY_NEW_MAGENTO_QUOTE_ID];
        }

        return null;
    }

    /**
     * Getting back the entry
     *
     * @return null|string
     */
    public function getOldQuoteId(): ?string
    {
        if ($this->entryExists()) {
            $customData = $this->customerSession->getCustomData();
            return $customData[self::SESSION_KEY_OLD_MAGENTO_QUOTE_ID];
        }

        return null;
    }
}
