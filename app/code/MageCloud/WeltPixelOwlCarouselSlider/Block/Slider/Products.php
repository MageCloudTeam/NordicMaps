<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\WeltPixelOwlCarouselSlider\Block\Slider;

use MageCloud\WeltPixelOwlCarouselSlider\ViewModel\OwlConfig;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Url\Encoder;
use Magento\Reports\Block\Product\Widget\Viewed\Proxy;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory;
use WeltPixel\OwlCarouselSlider\Block\Slider\Products as BaseProducts;
use WeltPixel\OwlCarouselSlider\Helper\Custom as HelperCustom;
use WeltPixel\OwlCarouselSlider\Helper\Products as HelperProducts;

/**
 * Class Products
 */
class Products extends BaseProducts
{
    /**
     * @var OwlConfig
     */
    private $owlConfig;

    /**
     * @var Encoder
     */
    private $urlEncoder;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param HelperProducts $helperProducts
     * @param HelperCustom $helperCustom
     * @param Visibility $catalogProductVisibility
     * @param ProductCollectionFactory $productsCollectionFactory
     * @param CollectionFactory $reportsCollectionFactory
     * @param Proxy $viewedProductsBlock
     * @param CategoryFactory $categoryFactory
     * @param OwlConfig $owlConfig
     * @param Encoder $urlEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperProducts $helperProducts,
        HelperCustom $helperCustom,
        Visibility $catalogProductVisibility,
        ProductCollectionFactory $productsCollectionFactory,
        CollectionFactory $reportsCollectionFactory,
        Proxy $viewedProductsBlock,
        CategoryFactory $categoryFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $flatState,
        OwlConfig $owlConfig,
        array $data = [],
        ?Encoder $urlEncoder = null
    ) {
        parent::__construct(
            $context,
            $helperProducts,
            $helperCustom,
            $catalogProductVisibility,
            $productsCollectionFactory,
            $reportsCollectionFactory,
            $viewedProductsBlock,
            $attributeRepository,
            $flatState,
            $data
        );
        $this->setTemplate('WeltPixel_OwlCarouselSlider::sliders/products.phtml');
        $this->owlConfig = $owlConfig;
        $this->urlEncoder = $urlEncoder ?? ObjectManager::getInstance()->get(Encoder::class);
    }

    /**
     * @return OwlConfig
     */
    public function getViewModel(): OwlConfig
    {
        return $this->owlConfig;
    }

    /**
     * @param $product
     *
     * @return array
     */
    public function getAddToCartPostParams($product): array
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($url),
            ]
        ];
    }
}
