<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Model\Cart\Validations;

use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\ItemGenerator;
use Klarna\Kco\Model\Cart\Validations\OrderTotal;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address;

/**
 * @internal
 */
class OrderTotalValidationPlugin
{
    /**
     * @var KssConfigProvider
     */
    private $config;
    /**
     * @var DataConverter
     */
    private $dataConverter;

    /**
     * @param KssConfigProvider $config
     * @param DataConverter     $dataConverter
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config, DataConverter $dataConverter)
    {
        $this->config        = $config;
        $this->dataConverter = $dataConverter;
    }

    /**
     * After plugin for the method getQuoteTotal()
     *
     * @param OrderTotal    $subject
     * @param int           $quoteTotal
     * @param DataObject    $request
     * @param CartInterface $quote
     * @return int
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGetQuoteTotal(
        OrderTotal $subject,
        $quoteTotal,
        DataObject $request,
        CartInterface $quote
    ): int {
        if (!$this->config->isKssEnabled($quote->getStore())) {
            return $quoteTotal;
        }
        $shippingAmount = $this->getShippingAmount($quote);
        if (!$this->isShippingFeeInRequest($request)) {
            $quoteTotal -= $shippingAmount;
        }
        return $quoteTotal;
    }

    /**
     * Get shipping amount from quote
     *
     * @param CartInterface $quote
     * @return int
     */
    private function getShippingAmount(CartInterface $quote): int
    {
        if ($quote->isVirtual()) {
            return 0;
        }
        /** @var Address $address */
        $address = $quote->getShippingAddress();
        return (int)$this->dataConverter->toApiFloat($address->getBaseShippingInclTax());
    }

    /**
     * Check to see if there is an order line type of shipping_fee in the request
     *
     * @param DataObject $request
     * @return bool
     */
    private function isShippingFeeInRequest(DataObject $request): bool
    {
        $orderLines = $request->getData('order_lines');
        if (empty($orderLines)) {
            return false;
        }

        foreach ($orderLines as $line) {
            if ($line['type'] === ItemGenerator::ITEM_TYPE_SHIPPING) {
                return true;
            }
        }
        return false;
    }
}
