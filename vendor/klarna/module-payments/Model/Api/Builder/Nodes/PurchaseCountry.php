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
use Magento\Quote\Api\Data\CartInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * @internal
 */
class PurchaseCountry
{
    /**
     * @var DirectoryHelper
     */
    private DirectoryHelper $directoryHelper;

    /**
     * @param DirectoryHelper $directoryHelper
     * @codeCoverageIgnore
     */
    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * Adding the purchase country to the request
     *
     * @param Builder $requestBuilder
     * @param CartInterface $magentoQuote
     */
    public function addToRequest(Builder $requestBuilder, CartInterface $magentoQuote): void
    {
        $country = $magentoQuote->getBillingAddress()->getCountry();
        if (empty($country)) {
            $country = $magentoQuote->getShippingAddress()->getCountry();
        }
        if (empty($country)) {
            $country = $this->directoryHelper->getDefaultCountry($magentoQuote->getStore());
        }

        $requestBuilder->setPurchaseCountry($country);
    }
}
