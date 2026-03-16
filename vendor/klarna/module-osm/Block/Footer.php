<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Onsitemessaging\Block;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class Footer extends Template
{
    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private MagentoToKlarnaLocaleMapper $localeResolver;

    /**
     * @param Context $context
     * @param MagentoToKlarnaLocaleMapper $locale
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(Context $context, MagentoToKlarnaLocaleMapper $locale, array $data = [])
    {
        parent::__construct($context, $data);
        $this->localeResolver  = $locale;
    }

    /**
     * Get the locale according to ISO_3166-1 standard
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Check to see if display on cart is enabled
     *
     * @return bool
     */
    public function showInFooter(): bool
    {
        return $this->isSetFlag('klarna/osm/enabled')
            && $this->isSetFlag('klarna/osm/footer_enabled');
    }

    /**
     * Wrapper around `$this->_scopeConfig->isSetFlag` that ensures store scope is checked
     *
     * @param string $path
     * @return bool
     */
    private function isSetFlag(string $path): bool
    {
        return $this->_scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
    }
}
