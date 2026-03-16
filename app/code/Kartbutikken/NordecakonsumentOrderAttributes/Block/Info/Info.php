<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\NordecakonsumentOrderAttributes\Block\Info;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

/**
 * Class Info
 */
class Info extends \Magento\Payment\Block\Info
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(Template\Context $context, Registry $coreRegistry, array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }
}