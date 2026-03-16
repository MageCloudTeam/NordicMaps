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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * @internal
 */
class Product extends PlacementAbstract
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @param MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper,
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($magentoToKlarnaLocaleMapper, $context, $data);

        $this->registry = $registry;
    }

    /**
     * Returns true if the button can be shown on the cart page
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isShowable(): bool
    {
        return $this->isKecEnabled() && $this->isShowablePosition('product');
    }

    /**
     * Getting back the product SKU
     *
     * @return string
     */
    public function getProductSku(): string
    {
        return $this->registry->registry('current_product')->getSku();
    }
}
