<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Model\Config\Design;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Header
 */
class Header
{
    /**
     * Configuration paths
     */
    const XML_CONFIG_HEADER_LOGO_SRC = 'design/header/logo_src';
    const XML_CONFIG_HEADER_LOGO_SMALL_SRC = 'design/header/header_logo_small_src';
    const XML_CONFIG_HEADER_LOGO_WIDTH = 'design/header/logo_width';
    const XML_CONFIG_HEADER_LOGO_HEIGHT = 'design/header/logo_height';
    const XML_CONFIG_HEADER_LOGO_ALT = 'design/header/logo_alt';
    const XML_CONFIG_HEADER_LOGO_SLOGAN = 'design/header/header_logo_slogan';
    const XML_CONFIG_HEADER_WELCOME = 'design/header/welcome';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Header constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return content design configuration header logo src
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function getLogoSrc(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_SRC, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header small logo src
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function getLogoSmallSrc(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_SMALL_SRC, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header logo width
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return integer
     */
    public function getLogoWidth(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_WIDTH, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header logo height
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return integer
     */
    public function getLogoHeight(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_HEIGHT, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header logo alt
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function getLogoAlt(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_ALT, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header logo slogan
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function getLogoSlogan(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_LOGO_SLOGAN, $scopeType, $scopeCode);
    }

    /**
     * Return content design configuration header welcome message
     *
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function getWelcome(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_HEADER_WELCOME, $scopeType, $scopeCode);
    }
}