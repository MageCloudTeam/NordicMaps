<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Model\Cart\Validations;

use Klarna\Kco\Model\Cart\Validations\ShippingAmount;
use Klarna\Orderlines\Model\ItemGenerator;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class ShippingAmountPlugin
{
    /**
     * @var KssConfigProvider
     */
    private $config;

    /**
     * @param KssConfigProvider $config
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config)
    {
        $this->config = $config;
    }

    /**
     * Subtracting the total amount of a used coupon (applied on shipping) from the shipping costs.
     *
     * @param ShippingAmount $subject
     * @param int            $result
     * @param DataObject     $request
     * @param CartInterface  $quote
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetShippingAmount(
        ShippingAmount $subject,
        int $result,
        DataObject $request,
        CartInterface $quote
    ): int {
        if (!$this->config->isKssEnabled($quote->getStore())) {
            return $result;
        }

        foreach ($request->getOrderLines() as $item) {
            if ($item['type'] === ItemGenerator::ITEM_TYPE_DISCOUNT) {
                $result += $item['total_amount'];
            }
        }
        return $result;
    }
}
