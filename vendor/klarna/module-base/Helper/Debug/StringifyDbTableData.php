<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper\Debug;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * @internal
 */
class StringifyDbTableData
{
    /**
     * @var int
     */
    private const QUERY_BUILDER_LIMIT = 1000;
    /**
     * @var Json
     */
    private Json $jsonSerializer;
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param Json $jsonSerializer
     * @param ResourceConnection $resourceConnection
     * @codeCoverageIgnore
     */
    public function __construct(
        Json               $jsonSerializer,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Returns a string representation of the data from the specified table
     *
     * @param string $tableName
     * @param array $queryBuilder it can contain the following
     *                           keys: conditions, orderBy, limit
     *                           Example:
     * <code>
     *  'conditions' => [
     *    ['field' => 'created_at', 'value' => strtotime('10 days ago')],
     *  ],
     *  'orderBy' => 'created_at DESC',
     *  'limit' => 1000
     * </code>
     *
     * @return string
     */
    public function getStringData(string $tableName, array $queryBuilder = []): string
    {
        $connection = $this->resourceConnection->getConnection();
        $query = $this->getQueryBuilder(
            $tableName,
            $queryBuilder['conditions'] ?? [],
            $queryBuilder['orderBy'] ?? null,
            $queryBuilder['limit'] ?? self::QUERY_BUILDER_LIMIT
        );
        $data = $connection->fetchAll($query);

        return $this->jsonSerializer->serialize($data);
    }

    /**
     * Returns a Select object for the specified table with the specified conditions
     *
     * @param string $tableName
     * @param array $conditions
     * @param string|null $orderBy
     * @param int $limit
     * @return Select
     */
    private function getQueryBuilder(
        string $tableName,
        array $conditions = [],
        string $orderBy = null,
        int $limit = self::QUERY_BUILDER_LIMIT
    ): Select {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName($tableName);
        $query = $connection->select()->from($tableName);

        foreach ($conditions as $condition) {
            $query->where($condition['field'], $condition['value']);
        }

        if ($orderBy) {
            $query->order($orderBy);
        }
        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }
}
