<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Block;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * @internal
 */
abstract class BaseAbstract extends Template
{
    public const CONFIG_KEC_ENABLED = 'payment/kec/enabled';

    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    protected MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper;

    /**
     * @param MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper
     * @param Context $context
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->magentoToKlarnaLocaleMapper = $magentoToKlarnaLocaleMapper;
    }

    /**
     * Returns true if the button can be shown on the cart page
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isKecEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            static::CONFIG_KEC_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()
        );
    }
}
