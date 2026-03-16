<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Fpt;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
class Calculator
{
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param Validator $validator
     * @codeCoverageIgnore
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get fpt data
     *
     * @param ExtensibleDataInterface $data
     * @return array
     */
    public function getFptData(ExtensibleDataInterface $data): array
    {
        $totalTax = 0;
        $name = [];
        $reference = [];

        foreach ($data->getAllItems() as $item) {
            if ($this->validator->isValidOrderItem($data, $item) &&
                !$this->validator->isValidQuoteItem($data, $item)) {
                continue;
            }

            $totalTax += $item->getWeeeTaxAppliedRowAmount();

            $tax = $item->getWeeeTaxApplied();
            if ($tax) {
                $weee = json_decode($tax, true);
                foreach ($weee as $tax) {
                    $name[]      = $tax['title'];
                    $reference[] = $tax['title'];
                }
            }
        }

        return [
            'tax' => $totalTax,
            'name' => array_unique($name),
            'reference' => array_unique($reference)
        ];
    }
}
