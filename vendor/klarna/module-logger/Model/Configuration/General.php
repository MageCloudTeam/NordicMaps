<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\Configuration;

use Klarna\Base\Model\Configuration\AbstractConfiguration;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class General extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = 'api';

    /**
     * Returns true if debugging is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isDebuggingEnabled(StoreInterface $store): bool
    {
        return $this->getKlarnaFlagValue($store, 'debug');
    }
}
