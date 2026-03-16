<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface as MageQuoteInterface;

/**
 * @internal
 */
class Quote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Get quote identifier by client_token
     *
     * @param string $clientToken
     * @return int|false
     */
    public function getIdByClientToken($clientToken)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'payments_quote_id')
                             ->where('client_token = :client_token');

        $bind = [':client_token' => (string)$clientToken];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get quote identifier by active Magento quote
     *
     * @param string $mageQuoteID
     * @return int|false
     * @throws LocalizedException
     */
    public function getActiveByQuoteId(string $mageQuoteID)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getMainTable(), 'payments_quote_id')
            ->where('is_active = 1')
            ->where('quote_id = :quote_id')
            ->order('payments_quote_id desc');

        $bind = [':quote_id' => $mageQuoteID];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get quote identifier by active Magento quote
     *
     * @param MageQuoteInterface $mageQuote
     * @return int|false
     * @throws LocalizedException
     */
    public function getActiveByQuote(MageQuoteInterface $mageQuote)
    {
        return $this->getActiveByQuoteId((string) $mageQuote->getId());
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init('klarna_payments_quote', 'payments_quote_id');
    }
}
