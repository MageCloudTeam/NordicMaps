<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\SnowdogMenuWidget\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ResourceModel\Menu\CollectionFactory;

/**
 * Class Menus
 */
class Menus implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Menus constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        $collection = $this->collectionFactory->create();
        $additional['value'] = MenuInterface::IDENTIFIER;
        $additional['label'] = MenuInterface::TITLE;

        foreach ($collection->getItems() as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }
}