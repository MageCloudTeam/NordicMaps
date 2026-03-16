<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Shipping;

use Klarna\Orderlines\Api\OrderLineInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Klarna\Orderlines\Model\ItemGenerator;

/**
 * Generate shipping order line details
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 */
class Handler implements OrderLineInterface
{
    /**
     * @var ItemGenerator
     */
    private ItemGenerator $itemGenerator;
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var Processor
     */
    private Processor $processor;

    /**
     * @param ItemGenerator $itemGenerator
     * @param Validator $validator
     * @param Processor $processor
     * @codeCoverageIgnore
     */
    public function __construct(
        ItemGenerator $itemGenerator,
        Validator $validator,
        Processor $processor
    ) {
        $this->itemGenerator = $itemGenerator;
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        if (!$this->validator->isCollectableForPrePurchase($dataHolder, $parameter)) {
            return $this;
        }

        $this->processor->processPrePurchase($dataHolder, $parameter);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        $this->processor->processPostPurchase($dataHolder, $parameter, $order);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetch(Parameter $parameter)
    {
        if ($this->validator->isFetchable($parameter)) {
            $parameter->addOrderLine($this->itemGenerator->getShippingItemRow($parameter, 1));
        }

        return $this;
    }
}
