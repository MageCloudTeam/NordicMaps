<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder\Nodes;

use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Base\Exception as KlarnaException;

/**
 * @internal
 */
class OrderLines
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
     * Adding the order lines to the request
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
            $message = 'Can not add the order line items to the request since the order line item list is empty ' .
                'for the Magento quote ID ' . $magentoQuote->getId();
            $this->logger->forceLogging($message);
            throw new KlarnaException(__($message));
        }

        $requestBuilder->addOrderlines($orderLines);
    }
}
