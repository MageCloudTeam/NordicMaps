<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\CheckoutConfig;

use Magento\Framework\UrlInterface;

/**
 * Getting back messages regarding the checkout settings
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
     * Getting back the message for the given stores when the title configuration is invalid
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidTitle(array $stores)
    {
        $urlKlarnaCheckout = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');
        $urlCustomer = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

        $message = '<strong>';
        $message .= __('Klarna Checkout title mandatory warning:');
        $message .= '</strong><p>';
        $message .= __(
            'The title value from the Klarna Checkout and customer settings are incorrectly configured. ' .
            'Either they should both be required or you can leave the setting as optional under customer ' .
            'settings. However, if the setting is “required” under customer settings then it must be set to ' .
            '“required” under Klarna Checkout as well.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Checkout Configuration</a> and ' .
            'here to go to the <a href="%2">Customer Configuration</a> to change your settings.',
            $urlKlarnaCheckout,
            $urlCustomer
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when the phone configuration is invalid
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidPhone(array $stores)
    {
        $urlKlarnaCheckout = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');
        $urlCustomer = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

        $message = '<strong>';
        $message .= __('Klarna Checkout phone mandatory warning:');
        $message .= '</strong><p>';
        $message .= __(
            'The phone value from the Klarna Checkout and customer settings are incorrectly configured. ' .
            'Either they should both be required or you can leave the setting as optional under customer ' .
            'settings. However, if the setting is “required” under customer settings then it must be set to ' .
            '“required” under Klarna Checkout as well.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Checkout Configuration</a> and ' .
            'here to go to the <a href="%2">Customer Configuration</a> to change your settings.',
            $urlKlarnaCheckout,
            $urlCustomer
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when the date of birth configuration is invalid
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidDateOfBirth(array $stores)
    {
        $urlKlarnaCheckout = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');
        $urlCustomer = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

        $message = '<strong>';
        $message .= __('Klarna Checkout date of birth mandatory warning:');
        $message .= '</strong><p>';
        $message .= __(
            'The date of birth value from the Klarna Checkout and customer settings are incorrectly configured. ' .
            'Either they should both be required or you can leave the setting as optional under customer ' .
            'settings. However, if the setting is “required” under customer settings then it must be set to ' .
            '“required” under Klarna Checkout as well.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Checkout Configuration</a> and ' .
            'here to go to the <a href="%2">Customer Configuration</a> to change your settings.',
            $urlKlarnaCheckout,
            $urlCustomer
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when there is no configured terms and conditions url
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidTermsUrl(array $stores): string
    {
        $urlKlarnaCheckout = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');

        $message = '<strong>';
        $message .= __('Klarna Checkout terms and conditions warning:');
        $message .= '</strong><p>';
        $message .= __(
            'No url for the terms and conditions is configured. ' .
            'Please setup a url.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Checkout Configuration</a> and ' .
            'change your settings.',
            $urlKlarnaCheckout
        );
        $message .= '</p>';

        return $message;
    }
}
