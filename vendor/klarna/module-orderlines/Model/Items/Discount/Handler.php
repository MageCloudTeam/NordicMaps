<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Discount;

use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\ItemGenerator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Klarna\Orderlines\Api\OrderLineInterface;

/**
 * @api
 */
class Handler implements OrderLineInterface
{
    /**
     * @var ItemGenerator
     */
    private $generator;
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var Processor
     */
    private Processor $processor;

    /**
     * @param ItemGenerator $generator
     * @param Validator $validator
     * @param Processor $processor
     * @codeCoverageIgnore
     */
    public function __construct(
        ItemGenerator $generator,
        Validator $validator,
        Processor $processor
    ) {
        $this->generator = $generator;
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        if (!$this->validator->isCollectableForPrePurchase($parameter, $dataHolder, $quote)) {
            return $this;
        }

        $this->processor->processPrePurchase($dataHolder, $parameter);
        return $this;
    }

    /**
     * We just return the instance itself since for the ordermanagement calls we don't need it.
     *
     * @param Parameter      $parameter
     * @param DataHolder     $dataHolder
     * @param OrderInterface $order
     * @return $this
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetch(Parameter $parameter)
    {
        if (!$this->validator->isFetchable($parameter)) {
            return $this;
        }

        $parameter->addOrderLine($this->generator->getDiscountItemRow($parameter, 1));
        return $this;
    }
}
