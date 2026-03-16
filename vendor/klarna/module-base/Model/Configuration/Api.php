<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Configuration;

use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class Api extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = 'api';

    /**
     * Getting back the merchant ID
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getMerchantId(StoreInterface $store): string
    {
        return $this->getKlarnaContentValue($store, 'merchant_id');
    }

    /**
     * Getting back the secret
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getSecret(StoreInterface $store): string
    {
        return $this->getKlarnaContentValue($store, 'shared_secret');
    }

    /**
     * Getting back the endpoint
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getEndpoint(StoreInterface $store): string
    {
        return $this->getKlarnaContentValue($store, 'api_version');
    }

    /**
     * Returns true if the test mode is used
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isTestMode(StoreInterface $store): bool
    {
        return $this->getKlarnaFlagValue($store, 'test_mode');
    }
}
