<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\WebsiteLocator\Model\Page;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class EntityList
 */
class EntityList
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string[]
     */
    private $entityTypes = [];

    /**
     * EntityList constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $entityTypes
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $entityTypes = []
    ) {
        $this->objectManager = $objectManager;
        $this->entityTypes = $entityTypes;
    }

    /**
     * @param string $type
     * @param array $data
     *
     * @return AbstractPage
     * @throws LocalizedException
     */
    public function getEntityByType(string $type, array $data): AbstractPage
    {
        if (!isset($this->entityTypes[$type])) {
            throw new LocalizedException(__('Entity type: %1 in not configured', $type));
        }

        $entity = $this->objectManager->create($this->entityTypes[$type], ['data' => $data]);

        if(!$entity instanceof TypeInterface) {
            throw new LocalizedException(__('Entity type: %1 in not implement %2', $type, TypeInterface::class));
        }

        return $entity;
    }
}
