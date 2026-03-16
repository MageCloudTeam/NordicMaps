<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @internal
 */
class Log extends AbstractDb
{
    /**
     * Initialization
     *
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('klarna_logs', 'log_id');
    }
}
