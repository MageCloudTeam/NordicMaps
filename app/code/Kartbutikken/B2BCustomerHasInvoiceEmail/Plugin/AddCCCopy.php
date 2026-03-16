<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\B2BCustomerHasInvoiceEmail\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class AddCCCopy
 */
class AddCCCopy
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Laminas\Validator\EmailAddress
     */
    private $emailValidator;

    /**
     * AddCCCopy constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Laminas\Validator\EmailAddress $emailValidator
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        \Laminas\Validator\EmailAddress $emailValidator
    )
    {
        $this->customerRepository = $customerRepository;
        $this->emailValidator = $emailValidator;
    }

    private function isEnabled($email, $websiteId)
    {
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

        $useInvoiceEmail = $customer->getCustomAttribute('use_invoice_email');
        $invoiceEmail = $customer->getCustomAttribute('invoice_email');

        return
            $useInvoiceEmail &&
            $useInvoiceEmail->getValue() &&
            $invoiceEmail &&
            $invoiceEmail->getValue() &&
            $this->emailValidator->isValid(trim($invoiceEmail->getValue()));
    }

    /**
     * Return email copy_to list
     *
     * @param $subject
     * @param $result
     *
     * @return array|bool
     */
    public function afterGetEmailCopyTo($subject, $result)
    {
        try {
            $customer = $this->customerRepository->get($subject->getCustomerEmail(), $subject->getStore()->getWebsiteId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return $result;
        }

        $invoiceEmail = $customer->getCustomAttribute('invoice_email');

        if ($this->isEnabled($subject->getCustomerEmail(), $subject->getStore()->getWebsiteId())) {
            return [$result, trim($invoiceEmail->getValue())];
        }

        return $result;
    }

    /**
     * Return email copy method
     *
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
//    public function afterGetCopyMethod($subject, $result)
//    {
//        if ($this->isEnabled($subject->getCustomerEmail(), $subject->getStore()->getWebsiteId())) {
//            return 'copy';
//        }
//
//        return $result;
//    }
}
