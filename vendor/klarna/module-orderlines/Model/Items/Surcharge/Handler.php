<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Surcharge;

use Klarna\Orderlines\Api\OrderLineInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Fpt\Validator as FptValidator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Orderlines\Model\ItemGenerator;

/**
 * @api
 */
class Handler implements OrderLineInterface
{
    /**
     * @var ItemGenerator
     */
    private ItemGenerator $itemGenerator;
    /**
     * @var FptValidator
     */
    private FptValidator $fptValidator;
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
     * @param FptValidator $fptValidator
     * @param Validator $validator
     * @param Processor $processor
     * @codeCoverageIgnore
     */
    public function __construct(
        ItemGenerator $itemGenerator,
        FptValidator $fptValidator,
        Validator $validator,
        Processor $processor
    ) {
        $this->itemGenerator = $itemGenerator;
        $this->fptValidator = $fptValidator;
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
     * @param Parameter $parameter
     * @param DataHolder $dataHolder
     * @return $this
     */
    public function collect(Parameter $parameter, DataHolder $dataHolder)
    {
        if (!$this->fptValidator->isFptUsable($dataHolder->getStore())) {
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
            $parameter->addOrderLine($this->itemGenerator->getSurchargeItemRow($parameter, 1));
        }

        return $this;
    }
}
