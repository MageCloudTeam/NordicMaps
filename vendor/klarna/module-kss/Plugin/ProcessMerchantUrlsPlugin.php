<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin;

use Klarna\Base\Api\BuilderInterface;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class ProcessMerchantUrlsPlugin
{
    /**
     * @var KssConfigProvider
     */
    private $config;

    /**
     * @param KssConfigProvider $config
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $config)
    {
        $this->config = $config;
    }

    /**
     * Remove shipping_option_update callback if KSS is enabled
     *
     * @param BuilderInterface $subject
     * @param array            $result
     * @param StoreInterface   $store
     * @param array            $urlParams
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterProcessMerchantUrls(BuilderInterface $subject, $result, $store, $urlParams): array
    {
        if ($this->config->isKssEnabled($store)) {
            unset($result['shipping_option_update']);
        }
        return $result;
    }
}
