<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update\Address;

use Klarna\Base\Model\Quote\Address\Handler;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Guest
{
    /**
     * @var Handler
     */
    private Handler $handler;

    /**
     * @param Handler $handler
     * @codeCoverageIgnore
     */
    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Setting the Klarna address data on the quote
     *
     * @param array $klarnaData
     * @param CartInterface $magentoQuote
     */
    public function applyKlarnaAddressData(array $klarnaData, CartInterface $magentoQuote): void
    {
        $this->handler->setBillingAddressDataFromArray($klarnaData['shipping_address'], $magentoQuote);
        $this->handler->setShippingAddressDataFromArray($klarnaData['shipping_address'], $magentoQuote);

        $billingAddress = $magentoQuote->getBillingAddress();
        $billingAddress->setEmail($klarnaData['shipping_address']['email']);
        $magentoQuote->setCustomerEmail($klarnaData['shipping_address']['email']);
    }
}
