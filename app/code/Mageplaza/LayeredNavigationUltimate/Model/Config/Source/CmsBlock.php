<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class CmsBlock
 * @package Mageplaza\LayeredNavigationUltimate\Model\Config\Source
 */
class CmsBlock implements ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

   /**
    * @var CollectionFactory
    */
    protected $collectionFactory;

  /**
   * @param CollectionFactory $collectionFactory
   */
   public function __construct(
       CollectionFactory $collectionFactory
   ) {
       $this->collectionFactory = $collectionFactory;
   }

    public function toOptionArray(){

        $options = array();
        $options[] = [
            'value' => '',
            'label' => __('-- Please select --')
        ];
        $collection = $this->collectionFactory->create();
        foreach($collection as $block){
            if($block->getData('is_active') !== '0'){
                $options[] = [
                    'value' => $block->getData('block_id'),
                    'label' => $block->getData('title')
                ];
            }
        }
        return $options;
    }
}
