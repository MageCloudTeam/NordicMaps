<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Plugin;

use Klarna\Base\Model\Filter\Sanitization;
use Magento\Framework\Exception\InputException;
use Magento\Config\Model\Config;
use Magento\Framework\Message\ManagerInterface;

/**
 * @internal
 */
class ConfigPlugin
{
    /**
     * @var Sanitization
     */
    private Sanitization $sanitization;

    /**
     * @param Sanitization $sanitization
     * @codeCoverageIgnore
     */
    public function __construct(Sanitization $sanitization)
    {
        $this->sanitization = $sanitization;
    }

    /**
     * Sanitizing the input
     *
     * @param Config $config
     * @return Config
     */
    public function beforeSave(
        Config $config
    ) {
        $configData = $config->getData();
        if (!$this->hasKlarnaKey($configData)) {
            return $config;
        }

        $configData = $this->sanitization->sanitizeKcoInput($configData);
        $configData = $this->sanitization->sanitizeKpInput($configData);
        $config->setData($configData);

        return $config;
    }

    /**
     * Returns true if the Klarna key is set
     *
     * @param array $configData
     * @return bool
     */
    private function hasKlarnaKey(array $configData): bool
    {
        if (!isset($configData['groups']['klarna_section'])) {
            return false;
        }

        return true;
    }
}
