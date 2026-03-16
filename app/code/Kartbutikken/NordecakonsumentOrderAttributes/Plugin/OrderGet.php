<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\NordecakonsumentOrderAttributes\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderGet
 */
class OrderGet
{
    /**
     * Get gift message
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->addAttributes($resultOrder);

        return $resultOrder;
    }

    /**
     * Get gift message
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $resultOrder
     * @return OrderSearchResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $resultOrder
    ) {
        array_filter(
            $resultOrder->getItems(),
            function (OrderInterface $order) {
                return $this->addAttributes($order);
            }
        );

        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     */
    private function addAttributes(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();

        if($extensionAttributes) {
            $extensionAttributes->setData('use_invoice_email', $order->getData('use_invoice_email'));
            $extensionAttributes->setData('invoice_email', $order->getData('invoice_email'));
            $extensionAttributes->setData('reference_code', $order->getData('reference_code'));
        }
    }
}