<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\TargetConfig;

use Magento\Framework\UrlInterface;

/**
 * Getting back messages regarding the target settings
 *
 * @internal
 */
class Message
{

    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;

    /**
     * @param UrlInterface $url
     * @codeCoverageIgnore
     */
    public function __construct(UrlInterface $url)
    {
        $this->urlBuilder = $url;
    }

    /**
     * Getting back the message for the given stores when the shipping country configuration is invalid
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidShippingCountries(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout shipping country configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'No shipping countries are enabled. ' .
            'Please enable at least one shipping country.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= "</p>";

        return $message;
    }

    /**
     * Getting back the message for the given stores when the customer group configuration is invalid
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidCustomerGroups(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout customer group configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'All customer groups are disabled. ' .
            'Please enable at least one customer group.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= "</p>";

        return $message;
    }
}
