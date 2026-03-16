<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Tax;

use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Fpt\Validator;
use Klarna\Orderlines\Model\Items\CalculatorAbstract;

/**
 * @internal
 */
class Calculator extends CalculatorAbstract
{
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param DataConverter $dataConverter
     * @param Validator $validator
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter, Validator $validator)
    {
        parent::__construct($dataConverter);
        $this->validator = $validator;
    }

    /**
     * Calculating the values
     *
     * @param DataHolder $dataHolder
     */
    public function calculate(DataHolder $dataHolder): void
    {
        $totalTax = $dataHolder->getTotalTax();
        if ($this->validator->isFptUsable($dataHolder->getStore())) {
            $totalTax += $dataHolder->getFptTax()['tax'];
        }

        $totalTaxConverted = $this->dataConverter->toApiFloat($totalTax);

        $this->reset()
            ->setUnitPrice($totalTaxConverted)
            ->setTaxAmount($totalTaxConverted);
    }
}
