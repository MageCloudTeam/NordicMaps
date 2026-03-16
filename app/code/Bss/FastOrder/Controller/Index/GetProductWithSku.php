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
class GetProductWithSku extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\FastOrder\Model\Search\Save
     */
    protected $saveModel;
    /**
     * @var \Bss\FastOrder\Helper\Data
     */
    protected $helperBss;

    /**
     * @var \Bss\FastOrder\Model\Search\ProductList
     */
    protected $productList;

    /**
     * Search constructor.
     *
     * @param Context $context
     * @param \Bss\FastOrder\Helper\Data $helperBss
     * @param \Bss\FastOrder\Model\Search\ProductList $productList
     * @param \Bss\FastOrder\Model\Search\Save $saveModel
     */
    public function __construct(
        Context $context,
        \Bss\FastOrder\Helper\Data $helperBss,
        \Bss\FastOrder\Model\Search\ProductList $productList,
        \Bss\FastOrder\Model\Search\Save $saveModel
    ) {
        parent::__construct($context);
        $this->helperBss = $helperBss;
        $this->productList = $productList;
        $this->saveModel = $saveModel;
    }

    /**
     * @return bool|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $productParams = $this->getRequest()->getParam('product');
        if (empty($productParams)) {
            $this->messageManager->addErrorMessage(
                __("Please select product(s).")
            );
            $resultJson->setData([]);
            return $resultJson;
        }

        $responseData = [];
        $skuErrors = [];

        foreach ($productParams as $key => $product) {
            $productData = $this->saveModel->getProductBySku($product['sku'], true);

            if (empty($productData)) {
                $skuErrors[] = $product['sku'];
            } else {
                array_push($responseData, $productData);
                $responseData[$key][1] = $product['qty'];
            }
        }

        if (!empty($skuErrors)) {
            $this->messageManager->addErrorMessage(
                __("SKU is not found or out of stock: %1", join(',', $skuErrors))
            );
        }

        $resultJson->setData($responseData);
        return $resultJson;
    }
}
