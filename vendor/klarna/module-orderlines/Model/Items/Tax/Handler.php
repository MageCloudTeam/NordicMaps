<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Tax;

use Klarna\Orderlines\Api\OrderLineInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Orderlines\Model\ItemGenerator;

/**
 * Generate tax order line details
 *
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
    public function __construct(ItemGenerator $itemGenerator, Validator $validator, Processor $processor)
    {
        $this->itemGenerator = $itemGenerator;
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * @inheritDoc
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * @inheritdoc
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * Collecting the values
     *
     * @param Parameter  $parameter
     * @param DataHolder $dataHolder
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    private function collect(Parameter $parameter, DataHolder $dataHolder)
    {
        if (!$this->validator->isCollectable($dataHolder)) {
            return $this;
        }

        $this->processor->process($dataHolder, $parameter);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetch(Parameter $parameter)
    {
        if ($this->validator->isFetchable($parameter)) {
            $parameter->addOrderLine($this->itemGenerator->getTaxItemRow($parameter, 1));
        }

        return $this;
    }
}
