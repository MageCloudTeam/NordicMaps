<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
declare(strict_types=1);

namespace MageCloud\EnhancedEcommerce\Block\Catalog\Product;

use MageCloud\EnhancedEcommerce\ViewModel\DataLayerViewModel;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Block\Product\ListProduct;

/**
 * Class ViewItemList
 * @package MageCloud\EnhancedEcommerce\Block\Catalog\Product
 */
class ViewItemList extends ListProduct
{
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function toHtml()
    {
        /** @var DataLayerViewModel $viewModel */
        $viewModel = $this->getData('viewModel');
        $eventArguments = [
            'store' => $this->_storeManager->getStore(),
            'collection' => $this->getLoadedProductCollection(),
            'item_list_name' => $this->getData('itemListName')
        ];
        return $viewModel->execute($eventArguments);
    }
}
