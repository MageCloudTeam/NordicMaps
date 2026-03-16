<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Quote extends AbstractDb
{
    /**
     * Get quote identifier by checkout_id
     *
     * @param string $checkoutId
     * @return string|false
     * @throws LocalizedException
     */
    public function getIdByCheckoutId(string $checkoutId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), 'kco_quote_id')
                             ->where('klarna_checkout_id = :klarna_checkout_id');

        $bind = [':klarna_checkout_id' => $checkoutId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Get quote identifier by active Magento quote
     *
     * @param CartInterface $mageQuote
     * @return string|false
     * @throws LocalizedException
     */
    public function getActiveByQuote(CartInterface $mageQuote)
    {
        $connection = $this->getConnection();

        $select = $connection
            ->select()
            ->from($this->getMainTable(), 'kco_quote_id')
            ->where('is_active = 1')
            ->where('quote_id = :quote_id')
            ->order('kco_quote_id desc');

        $bind = [':quote_id' => (string)$mageQuote->getId()];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('klarna_kco_quote', 'kco_quote_id');
    }
}
