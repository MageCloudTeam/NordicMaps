<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\Api;

/**
 * @api
 */
interface BarManagementInterface
{
    /**
     * @param int $customerGroup
     * @param string $page
     * @param int[] $position
     *
     * @return \Amasty\ShippingBar\Api\Data\ProfileInterface
     */
    public function getFilledData($customerGroup, $page, $position);
}
