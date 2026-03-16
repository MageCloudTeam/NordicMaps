<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\DesignConfig;

use Magento\Framework\UrlInterface;

/**
 * Getting back messages regarding the design settings
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
     * Getting back the message for the given stores when the link color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidLinkColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('link color');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the message for the given stores when the header color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidHeaderColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('header color');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the message for the given stores when the checkbox checkmark color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidCheckboxCheckmarkColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('checkbox checkmark color');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the message for the given stores when the checkbox color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidCheckboxColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('checkbox');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the message for the given stores when the button text color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidButtonTextColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('button text');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the message for the given stores when the button color has a invalid value
     *
     * @param array $stores
     * @return string
     */
    public function getMessageInvalidButtonColor(array $stores): string
    {
        $message = $this->getPrefixMessage();
        $message .= $this->getCoreMessage('button color');
        $message .= '</p><p>';
        $message .= $this->getSuffixMessage($stores);

        return $message;
    }

    /**
     * Getting back the prefix text for the message
     *
     * @return string
     */
    private function getPrefixMessage(): string
    {
        $message = '<strong>';
        $message .= __('Klarna Checkout design warning:');
        $message .= '</strong><p>';

        return $message;
    }

    /**
     * Getting back the suffix text for the message
     *
     * @param array $stores
     * @return string
     */
    private function getSuffixMessage(array $stores): string
    {
        $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/checkout');

        $message = __('Store(s) affected: ');
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Klarna Design Configuration</a> and ' .
            'change your settings.',
            $url
        );
        $message .= "</p>";

        return $message;
    }

    /**
     * Getting back the core message content
     *
     * @param string $element
     * @return string
     */
    private function getCoreMessage($element): string
    {
        return __('An invalid CSS hex color code is used for the %1. ' .
            'Please change it to a valid css hex color code.', $element)->render();
    }
}
