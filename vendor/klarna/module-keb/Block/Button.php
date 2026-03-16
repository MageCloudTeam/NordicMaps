<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class Button extends Template
{
    public const CONFIG_KEB_THEME = 'klarna/keb/theme';
    public const CONFIG_KEB_LABEL = 'klarna/keb/label';
    public const CONFIG_KEB_SHAPE = 'klarna/keb/shape';
    public const CONFIG_KEB_ENABLED_MINICART = 'klarna/keb/enabled_minicart';
    public const CONFIG_KEB_ENABLED_CART = 'klarna/keb/enabled_cart';
    public const CONFIG_KEB_ENABLED = 'klarna/keb/enabled';

    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private MagentoToKlarnaLocaleMapper $localeResolver;

    /**
     * @param Context $context
     * @param MagentoToKlarnaLocaleMapper $localeResolver
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context                     $context,
        MagentoToKlarnaLocaleMapper $localeResolver,
        array                       $data = []
    ) {
        parent::__construct($context, $data);
        $this->localeResolver = $localeResolver;
    }

    /**
     * Get the button theme
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTheme(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEB_THEME,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Get the button label
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLabel(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEB_LABEL,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Get the button shape
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getShape(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEB_SHAPE,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Returns true if KEB is enabled on the minicart
     *
     * @return bool
     */
    public function isEnabledOnMiniCart(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            static::CONFIG_KEB_ENABLED_MINICART,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Returns true if KEB is enabled on the cart page
     *
     * @return bool
     */
    public function isEnabledOnCartPage(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            static::CONFIG_KEB_ENABLED_CART,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
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
}
