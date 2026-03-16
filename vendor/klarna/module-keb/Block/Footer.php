<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Block;

use Klarna\Base\Model\Configuration\Api;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class Footer extends Template
{
    public const CONFIG_KEB_ENABLED     = 'klarna/keb/enabled';
    public const CONFIG_KEB_ENVIRONMENT = 'klarna/keb/environment';

    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param Context      $context
     * @param Api          $apiConfiguration
     * @param array        $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context      $context,
        Api $apiConfiguration,
        array        $data = []
    ) {
        parent::__construct($context, $data);
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Get enabled status
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isKebEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            static::CONFIG_KEB_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Get the environment
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEnvironment(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEB_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Get the MID
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMid(): string
    {
        $result = $this->apiConfiguration->getMerchantId($this->_storeManager->getStore());
        return explode('_', $result)[0];
    }

    /**
     * Get the express button library url
     *
     * @return string
     */
    public function getJsUrl(): string
    {
        return 'https://x.klarnacdn.net/express-button/v1/lib.js';
    }

    /**
     * Get the callback ajax url
     *
     * @return string
     */
    public function getKebUrl(): string
    {
        return $this->_urlBuilder->getUrl('keb/index/index');
    }
}
