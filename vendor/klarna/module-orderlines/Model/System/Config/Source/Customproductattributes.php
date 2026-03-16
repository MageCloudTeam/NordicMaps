<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\System\Config\Source;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeFrontendLabelInterface;

/**
 * @internal
 */
class Customproductattributes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @param SearchCriteriaBuilder               $searchCriteriaBuilder
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        $this->searchCriteriaBuilder      = $searchCriteriaBuilder;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * This method extracts frontend label from FrontendLabel object for admin store.
     *
     * @param ProductAttributeInterface $attribute
     * @return string|null
     */
    private function extractAdminStoreFrontendLabel(ProductAttributeInterface $attribute): ?string
    {
        $frontendLabel  = [];
        $frontendLabels = $attribute->getFrontendLabels();
        if (empty($frontendLabels)) {
            return $attribute->getFrontendLabel();
        }
        if (isset($frontendLabels[0]) && $frontendLabels[0] instanceof AttributeFrontendLabelInterface) {
            foreach ($attribute->getFrontendLabels() as $label) {
                $frontendLabel[$label->getStoreId()] = $label->getLabel();
            }
        }
        return $frontendLabel[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (!empty($this->options)) {
            return $this->options;
        }

        // NOTE: It would be better to use `->addFilter('backend_type', ['decimal', 'int'], 'in')` here, but since the
        // admin doesn't allow creating attributes with backend_type set to int we can't
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $listInfo = $this->productAttributeRepository->getList($searchCriteria);
        foreach ($listInfo->getItems() as $item) {
            $this->options[] = [
                'value' => $item->getAttributeCode(),
                'label' => $this->extractAdminStoreFrontendLabel($item)
            ];
        }
        return $this->options;
    }
}
