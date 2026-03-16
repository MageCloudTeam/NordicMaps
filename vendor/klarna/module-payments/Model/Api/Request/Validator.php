<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;

/**
 * @internal
 */
class Validator
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Setting the data
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Checking if a required value is missing
     *
     * @param array $requiredAttributes
     * @param string $type
     * @return bool
     * @throws KlarnaApiException
     */
    public function isRequiredValueMissing(array $requiredAttributes, string $type): bool
    {
        $missingAttributes = [];
        foreach ($requiredAttributes as $requiredAttribute) {
            if (!isset($this->data[$requiredAttribute])) {
                $missingAttributes[] = $requiredAttribute;
                continue;
            }
            if (null === $this->data[$requiredAttribute]) {
                $missingAttributes[] = $requiredAttribute;
            }
            if (is_array($this->data[$requiredAttribute]) && count($this->data[$requiredAttribute]) === 0) {
                $missingAttributes[] = $requiredAttribute;
            }
        }
        if (!empty($missingAttributes)) {
            throw new KlarnaApiException(
                __(
                    'Missing required attribute(s) on %1: "%2".',
                    $type,
                    implode(', ', $missingAttributes)
                )
            );
        }

        return true;
    }

    /**
     * Checking if the sum of the order lines is matching the order amount
     *
     * @return bool
     * @throws KlarnaApiException
     */
    public function isSumOrderLinesMatchingOrderAmount(): bool
    {
        $keyOrderLines = 'order_lines';
        $keyOrderAmount = 'order_amount';
        if (!isset($this->data[$keyOrderLines])) {
            throw new KlarnaApiException(__('Order line totals are not set'));
        }
        if (!isset($this->data[$keyOrderLines]) || !isset($this->data[$keyOrderAmount])) {
            throw new KlarnaApiException(__('Order amount is not set'));
        }

        $total = 0;
        foreach ($this->data['order_lines'] as $orderLine) {
            $total += (int)$orderLine['total_amount'];
        }

        if ($total !== $this->data['order_amount']) {
            throw new KlarnaApiException(
                __('Order line totals do not total order_amount - %1 != %2', $total, $this->data['order_amount'])
            );
        }

        return true;
    }
}
