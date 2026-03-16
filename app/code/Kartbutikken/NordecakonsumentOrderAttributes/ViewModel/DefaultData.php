<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\NordecakonsumentOrderAttributes\ViewModel;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class DefaultData
 */
class DefaultData implements ArgumentInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * DefaultData constructor.
     *
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return array
     */
    public function getOrderAttributes(): array
    {
        return [
            'use_invoice_email' => !!$this->getValue('use_invoice_email'),
            'invoice_email'     => $this->getValue('invoice_email'),
        ];
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    protected function getValue(string $key): ?string
    {
        $attribute = $this->getCustomer()->getDataModel()->getCustomAttribute($key);

        return $attribute ? $attribute->getValue() : null;
    }
}