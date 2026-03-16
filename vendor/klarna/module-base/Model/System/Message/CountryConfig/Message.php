<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Message\CountryConfig;

use Magento\Framework\UrlInterface;

/**
 * Getting back messages regarding the country settings
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
     * Getting back the message for the given stores where specific countries require a region
     *
     * @param array $stores
     * @return string
     */
    public function getMessageFailedRegionCountries(array $stores)
    {
        $message = '';
        $adminRegionUrl = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/general');
        $message .= '<strong>';
        $message .= __(
            'Klarna module country region warning:'
        );
        $message .= '</strong><p>';
        $message .= __('For the following stores the countries are configured to require a region:') . '<p>';
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Country Configuration</a> and change your settings.',
            $adminRegionUrl
        );
        $message .= "</p>";

        return $message;
    }

    /**
     * Getting back the message for the given stores where no store country is selected
     *
     * @param array $stores
     * @return string
     */
    public function getMessageNoStoreCountrySelected(array $stores)
    {
        $message = '';

        $adminStoreInfoUrl = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/general');
        $message .= '<strong>';
        $message .= __(
            'Klarna module store information warning:'
        );
        $message .= '</strong><p>';
        $message .= __('The following stores require a selected country in the store information') . '<p>';
        $message .= implode(', ', $stores);
        $message .= '</p><p>';
        $message .= __(
            'Click here to go to <a href="%1">Store Information</a> and change your settings.',
            $adminStoreInfoUrl
        );
        $message .= "</p>";

        return $message;
    }
}
