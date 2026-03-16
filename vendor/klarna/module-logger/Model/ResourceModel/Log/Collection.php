<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Klarna\Logger\Model\ResourceModel\Log as LogResourceModel;
use Klarna\Logger\Model\Log as LogModel;

/**
 * @internal
 */
class Collection extends AbstractCollection
{
    /**
     * Initialization
     *
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init(LogModel::class, LogResourceModel::class);
    }
}
