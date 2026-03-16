<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Plugin\Sales\Block\Adminhtml\Order\View;

use Klarna\Base\Model\Payment\EnablementChecker;

/**
 * @internal
 */
class InfoPlugin
{
    /**
     * @var EnablementChecker
     */
    private EnablementChecker $enablementChecker;

    /**
     * @param EnablementChecker $enablementChecker
     * @codeCoverageIgnore
     */
    public function __construct(EnablementChecker $enablementChecker)
    {
        $this->enablementChecker = $enablementChecker;
    }

    /**
     * Wrapper around getAddressEditLink() so that we don't allow editing orders paid for using KP method types
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param callable                                       $proceed
     * @param \Magento\Sales\Model\Order\Address             $address
     * @param string                                         $label
     *
     * @return string
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function aroundGetAddressEditLink(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $proceed,
        $address,
        $label = ''
    ) {
        $result = $proceed($address, $label);
        if ($this->enablementChecker->ispPaymentMethodInstanceCodeStartsWithKlarna(
            $address->getOrder()->getPayment()
        )) {
            return '';
        }
        return $result;
    }
}
