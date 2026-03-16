<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin;

use Klarna\Base\Helper\DataConverter;
use Klarna\Orderlines\Model\ItemGenerator;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Base\Api\BuilderInterface;

/**
 * @internal
 */
class KasperPlugin
{
    /**
     * @var KssConfigProvider
     */
    private $config;
    /**
     * @var DataConverter
     */
    private $dataConverter;

    /**
     * @param KssConfigProvider $config
     * @param DataConverter     $dataConverter
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config, DataConverter $dataConverter)
    {
        $this->config        = $config;
        $this->dataConverter = $dataConverter;
    }

    /**
     * Updating the values for the create request
     *
     * @param BuilderInterface $subject
     * @param BuilderInterface $result
     * @param CartInterface    $quote
     * @return BuilderInterface
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGenerateCreateRequest(
        BuilderInterface $subject,
        $result,
        CartInterface $quote
    ) {
        return $this->updateValues($subject, $result, $quote);
    }

    /**
     * Updating the values for the update request
     *
     * @param BuilderInterface $subject
     * @param BuilderInterface $result
     * @param CartInterface    $quote
     * @return BuilderInterface
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGenerateUpdateRequest(
        BuilderInterface $subject,
        $result,
        CartInterface $quote
    ) {
        return $this->updateValues($subject, $result, $quote);
    }

    /**
     * Updating the values for different request types
     *
     * @param BuilderInterface $subject
     * @param BuilderInterface $result
     * @param CartInterface    $quote
     * @return BuilderInterface
     */
    private function updateValues(
        BuilderInterface $subject,
        BuilderInterface $result,
        CartInterface $quote
    ): BuilderInterface {
        if (!$this->config->isKssEnabled($quote->getStore())) {
            return $result;
        }

        $request = $subject->getParameter()->getRequest();
        $request['order_amount'] -= $this->dataConverter->toApiFloat(
            $quote->getShippingAddress()->getBaseShippingInclTax()
        );

        $request = $this->calculateOrderTaxAmount($request, $quote);
        $subject->getParameter()->setRequest($request);

        return $result;
    }

    /**
     * Calculating the order tax amount.
     * If there is a discount line because a coupon applied on shipping was used then we will subtract the total tax
     * amount from the order tax amount
     *
     * @param array         $request
     * @param CartInterface $quote
     * @return array
     */
    private function calculateOrderTaxAmount(array $request, CartInterface $quote): array
    {
        $request['order_tax_amount'] -= $this->dataConverter->toApiFloat(
            $quote->getShippingAddress()->getBaseShippingTaxAmount()
        );

        foreach ($request['order_lines'] as $row) {
            if ($row['type'] === ItemGenerator::ITEM_TYPE_DISCOUNT) {
                $request['order_tax_amount'] += $row['total_tax_amount'];
            }
        }

        return $request;
    }
}
