<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
abstract class PlacementAbstract extends BaseAbstract
{
    public const CONFIG_KEC_THEME = 'payment/kec/theme';
    public const CONFIG_KEC_SHAPE = 'payment/kec/shape';
    public const CONFIG_KEC_CLIENT_IDENTIFIER = 'payment/kec/client_identifier';
    public const CONFIG_KEC_POSITION = 'payment/kec/position';

    /**
     * Returns true if its showable
     *
     * @return bool
     */
    abstract public function isShowable(): bool;

    /**
     * Getting back the theme
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEC_THEME,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Getting back the shape
     *
     * @return string
     */
    public function getShape(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEC_SHAPE,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Getting back the locale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->magentoToKlarnaLocaleMapper->getLocale();
    }

    /**
     * Getting back the client key
     *
     * @return string
     */
    public function getClientKey(): string
    {
        return $this->_scopeConfig->getValue(
            static::CONFIG_KEC_CLIENT_IDENTIFIER,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }

    /**
     * Returns true if KEC can be shown on the respective position
     *
     * @param string $position
     * @return bool
     */
    protected function isShowablePosition(string $position): bool
    {
        $list = $this->_scopeConfig->getValue(
            static::CONFIG_KEC_POSITION,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );

        if ($list === null) {
            return false;
        }

        $listExploded = explode(',', $list);
        return in_array($position, $listExploded);
    }
}
