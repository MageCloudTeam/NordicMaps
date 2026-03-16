<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Special Promotions Base for Magento 2
 */

namespace Amasty\Rules\Model\Rule\SkipItemsValidator;

use Amasty\Rules\Model\ConfigModel;
use Amasty\Rules\Model\ResourceModel\Product\CatalogPriceRule;
use Amasty\Rules\Model\Rule\Action\Discount\AbstractRule;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;

class SpecialPriceValidator implements SkipItemValidatorInterface
{
    /**
     * @var ConfigModel
     */
    private $configModel;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CatalogPriceRule
     */
    private $catalogPriceRule;

    public function __construct(
        CatalogPriceRule $catalogPriceRule,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        ConfigModel $configModel
    ) {
        $this->catalogPriceRule = $catalogPriceRule;
        $this->configModel = $configModel;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    public function validate(AbstractItem $item, Rule $rule): bool
    {
        $product = $item->getProduct();

        if ($product->getSpecialPrice()) {
            return true;
        }

        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $product = current($item->getChildren())->getProduct();
        }

        return $this->hasCatalogPriceRule($product);
    }

    public function isNeedToValidate(Rule $rule): bool
    {
        $amrule = $rule->getData(AbstractRule::AMASTY_RULE);
        $useGeneralSkipSettings = $amrule->isEnableGeneralSkipSettings();
        $skipConditions = explode(',', (string)$amrule->getSkipRule());

        return ($useGeneralSkipSettings && $this->configModel->getSkipSpecialPrice())
            || (!$useGeneralSkipSettings
                && in_array(SkipItemValidatorInterface::SPECIAL_PRICE, $skipConditions, true));
    }

    private function hasCatalogPriceRule(Product $product): bool
    {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $groupId = (int)$this->customerSession->getCustomerGroupId();

        if ($this->catalogPriceRule->getCatalogRuleProduct((int)$product->getId(), $websiteId, $groupId)) {
            return true;
        }

        return false;
    }
}
