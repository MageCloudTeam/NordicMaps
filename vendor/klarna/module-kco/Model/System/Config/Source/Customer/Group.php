<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Config\Source\Customer;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * @internal
 */
class Group extends Table
{
    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;
    /**
     * @var DataObject
     */
    private $converter;

    /**
     * @param CollectionFactory        $collectionFactory
     * @param OptionFactory            $attrOptionFactory
     * @param GroupManagementInterface $groupManagement
     * @param DataObject               $converter
     * @codeCoverageIgnore
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        OptionFactory $attrOptionFactory,
        GroupManagementInterface $groupManagement,
        DataObject $converter
    ) {
        $this->groupManagement = $groupManagement;
        $this->converter       = $converter;
        parent::__construct($collectionFactory, $attrOptionFactory);
    }

    /**
     * Return array of customer groups
     *
     * @param bool $withEmpty Add empty option to array
     * @param bool $defaultValues
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        if (!$this->_options) {
            $groups = $this->groupManagement->getLoggedInGroups();
            array_unshift($groups, $this->groupManagement->getNotLoggedInGroup());
            $this->_options = $this->converter->toOptionArray($groups, 'id', 'code');
            array_unshift($this->_options, ['value' => -1, 'label' => ' ']);
        }
        return $this->_options;
    }
}
