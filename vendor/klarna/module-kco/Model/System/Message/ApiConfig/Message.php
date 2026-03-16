<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\ApiConfig;

use Magento\Framework\UrlInterface;

/**
 * Getting back messages regarding the api configurations
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
     * Getting back all messages where specific conditions are fulfilled
     *
     * @param array $validationResult
     * @return string
     */
    public function getMessages(array $validationResult): string
    {
        $message = '';
        if (!empty($validationResult['kp_kco_enabled'])) {
            $message .= $this->getMessageKpKcoEnabled($validationResult['kp_kco_enabled']);
        }
        if (!empty($validationResult['api_not_kco'])) {
            $message .= $this->getMessageNoKcoApiVersionSelected($validationResult['api_not_kco']);
        }
        if (!empty($validationResult['merchant_id_empty'])) {
            $message .= $this->getMessageEmptyMerchantId($validationResult['merchant_id_empty']);
        }
        if (!empty($validationResult['password_empty'])) {
            $message .= $this->getMessageEmptyPassword($validationResult['password_empty']);
        }
        return $message;
    }

    /**
     * Getting back the message for the given stores when kp and kco is enabled
     *
     * @param array $stores
     * @return string
     */
    private function getMessageKpKcoEnabled(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout module api configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'You cannot use both Klarna Checkout and Klarna Payments in the same website. ' .
            'Please disable one of them and try again.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when no KCO api version is selected
     *
     * @param array $stores
     * @return string
     */
    private function getMessageNoKcoApiVersionSelected(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout module api configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'When using Klarna Checkout, please select a "Klarna Checkout" API version ' .
            'instead of a "Klarna Payments" one.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when no password is configured
     *
     * @param array $stores
     * @return string
     */
    private function getMessageEmptyPassword(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout module api configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'No password is configured. For a working Klarna Checkout you need to setup a password.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= '</p>';

        return $message;
    }

    /**
     * Getting back the message for the given stores when no merchant id is configured
     *
     * @param array $stores
     * @return string
     */
    private function getMessageEmptyMerchantId(array $stores): string
    {
        $urlPayments = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

        $message = '<strong>';
        $message .= __('Klarna Checkout module api configuration warning:');
        $message .= '</strong><p>';
        $message .= __(
            'No merchant id is configured. For a working Klarna Checkout you need to setup a merchant id.'
        ) . '</p><p>';
        $message .= __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
            $urlPayments
        );
        $message .= '</p>';

        return $message;
    }
}
