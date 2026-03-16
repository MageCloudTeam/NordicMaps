<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Cookie Consent (GDPR) for Magento 2
 */

namespace Amasty\GdprCookie\Model\ResourceModel;

use Amasty\GdprCookie\Api\Data\CookieInterface;
use Amasty\GdprCookie\Model\StoreData\Hydrator;
use Amasty\GdprCookie\Model\StoreData\ScopedFieldsProvider;
use Magento\Framework\Model\ResourceModel\Db;

class CookieGroup extends Db\AbstractDb
{
    public const TABLE_NAME = 'amasty_gdprcookie_group';
    public const STORE_DATA_TABLE_NAME = 'amasty_gdprcookie_group_store_data';

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @var ScopedFieldsProvider
     */
    private $scopedFieldsProvider;

    /**
     * Used during object hydration with store data
     * @var int
     */
    private $storeId = 0;

    public function __construct(
        Db\Context $context,
        Hydrator $hydrator,
        ScopedFieldsProvider $scopedFieldsProvider,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->hydrator = $hydrator;
        $this->scopedFieldsProvider = $scopedFieldsProvider;
    }

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    public function load(\Magento\Framework\Model\AbstractModel $group, $value, $field = null)
    {
        parent::load($group, $value, $field);

        if ($storeId = (int)$this->storeId) {
            $cookieGroupStoreData = $this->getConnection()->fetchRow(
                $this->getConnection()->select()
                    ->from($this->getTable(self::STORE_DATA_TABLE_NAME))
                    ->where('group_id = ?', (int)$group->getId())
                    ->where('store_id = ?', $storeId)
            );

            if ($cookieGroupStoreData) {
                $this->hydrator->hydrateStoreData($group, $cookieGroupStoreData);
            }
        }

        return $this;
    }

    public function save(\Magento\Framework\Model\AbstractModel $group)
    {
        $storeId = (int)$this->storeId;
        $groupId = (int)$group->getId();
        $cookieIds = (array)$group->getData('cookies');

        if ($storeId) {
            $cookieGroupStoreData = array_intersect_key(
                $group->getData(),
                array_flip($this->scopedFieldsProvider->getScopedFields($this->getMainTable()))
            );
            $cookieGroupStoreData['group_id'] = $groupId;
            $cookieGroupStoreData['store_id'] = $storeId;
            $this->getConnection()->beginTransaction();
            $this->getConnection()->delete(
                $this->getTable(self::STORE_DATA_TABLE_NAME),
                sprintf('group_id = %s AND store_id = %s', $groupId, $storeId)
            );
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(self::STORE_DATA_TABLE_NAME),
                $cookieGroupStoreData
            );
            $this->getConnection()->commit();
            $group->afterSave();
        } else {
            parent::save($group);
        }

        $this->saveRelations($storeId, (int)$group->getId(), $cookieIds);
    }

    private function saveRelations(int $storeId, int $groupId, array $cookieIds)
    {
        $table = $this->getTable(Cookie::TABLE_NAME);
        $updateWherePart = [CookieInterface::GROUP_ID . ' = ?' => $groupId];

        if ($storeId) {
            $table = $this->getTable(Cookie::STORE_DATA_TABLE_NAME);
            $updateWherePart['store_id = ?'] = $storeId;
        }

        $this->getConnection()->update(
            $table,
            [CookieInterface::GROUP_ID => null],
            $updateWherePart
        );

        if ($cookieIds) {
            if ($storeId) {
                $cookieData = array_map(function ($cookieId) use ($storeId, $groupId) {
                    return [
                        'cookie_id' => $cookieId,
                        'store_id' => $storeId,
                        'group_id' => $groupId
                    ];
                }, $cookieIds);
            } else {
                $cookieData = array_map(function ($cookieId) use ($groupId) {
                    return [
                        CookieInterface::ID => $cookieId,
                        'group_id' => $groupId
                    ];
                }, $cookieIds);
            }

            $this->getConnection()->insertOnDuplicate(
                $table,
                $cookieData
            );
        }
    }

    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }
}
