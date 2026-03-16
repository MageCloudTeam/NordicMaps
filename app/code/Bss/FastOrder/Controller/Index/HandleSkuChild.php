<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_FastOrder
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\FastOrder\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class GetProductWithSku
 *
 * @package Bss\FastOrder\Controller\Index
 */
class HandleSkuChild extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Bss\FastOrder\Helper\ConfigurableProduct
     */
    protected $configurableProductHelper;

    /**
     * HandleSkuChild constructor.
     * @param Context $context
     * @param \Bss\FastOrder\Helper\ConfigurableProduct $configurableProductHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        \Bss\FastOrder\Helper\ConfigurableProduct $configurableProductHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
    
        parent::__construct($context);
        $this->configurableProductHelper = $configurableProductHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $queryText = $this->getRequest()->getParam('skuList');
        $data = $this->getProductManual($queryText);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * @param array $productParams [[SKU, Qty]]
     * @return array
     */
    private function getProductManual($productParams)
    {
        $productConfigurable = [];
        $productOther = [];
        $responseData = [];
        $itemAdded = 0;

        try {
            foreach ($productParams as $productParam) {
                $params = explode(':', $productParam);
                $sku = $params[0];
                try {
                    $qty = $params[1];
                    $product = $this->productRepository->get($sku);
                    $parentId = $this->configurableProductHelper->getParentProductId($product->getId());
                    if ($parentId) {
                        $productConfigurable[$parentId][] = ['sku' => $product->getSku(), 'qty' => $qty];
                    } else {
                        $productOther[] = ['sku' => $product->getSku(), 'qty' => $qty];
                    }
                    $itemAdded++;
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(
                        __("%1 do not match or wrong format SKU:Qty", $sku)
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __("%1 do not match or wrong format SKU:Qty", $sku)
                    );
                }
            }

            if ($itemAdded > 0) {
                $this->messageManager->addSuccessMessage(__('List was updated'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __("SKU not found or wrong format SKU:Qty")
            );
        }

        $responseData['childProduct'] = $productConfigurable;
        $responseData['otherProduct'] = $productOther;
        return $responseData;
    }
}