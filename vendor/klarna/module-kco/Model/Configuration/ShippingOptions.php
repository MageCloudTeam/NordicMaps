<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Configuration;

use Klarna\Base\Model\Configuration\AbstractConfiguration;
use Klarna\Kco\Model\Payment\Kco;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class ShippingOptions extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = Kco::METHOD_CODE;

    /**
     * Returns true if a separate shipping address is allowed to be used
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isSeparateShippingAddressAllowed(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'separate_address');
    }
}
