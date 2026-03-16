<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package GDPR Base for Magento 2
 */

namespace Amasty\Gdpr\Model\ResourceModel\ConsentQueue;

use Amasty\Gdpr\Model\ConsentQueue as ConsentQueueModel;
use Amasty\Gdpr\Model\ResourceModel\ConsentQueue as ConsentQueueResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(ConsentQueueModel::class, ConsentQueueResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
