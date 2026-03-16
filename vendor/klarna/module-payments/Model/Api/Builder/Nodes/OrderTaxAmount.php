<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class OrderTaxAmount
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adding the order tax amount to the request
     *
     * @param Builder $requestBuilder
     * @param Parameter $parameter
     * @param CartInterface $magentoQuote
     * @throws KlarnaException
     */
    public function addToRequest(Builder $requestBuilder, Parameter $parameter, CartInterface $magentoQuote): void
    {
        $orderLines = $parameter->getOrderLines();
        if (empty($orderLines)) {
            $message = 'Can not generate the order tax amount since the order line item list is empty ' .
                'for the Magento quote ID ' . $magentoQuote->getId();
            $this->logger->forceLogging($message);
            throw new KlarnaException(__($message));
        }

        $orderTaxAmount = 0;
        foreach ($orderLines as $item) {
            if ($item['type'] === 'sales_tax') {
                $orderTaxAmount = $item['total_amount'];
                break;
            }
            $orderTaxAmount += $item['total_tax_amount'];
        }

        $requestBuilder->setOrderTaxAmount((int) $orderTaxAmount);
    }
}
