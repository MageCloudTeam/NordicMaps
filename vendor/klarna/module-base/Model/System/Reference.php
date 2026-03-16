<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System;

use Klarna\Base\Helper\VersionInfo;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;

/**
 * @internal
 */
class Reference extends Field
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;
    /**
     * @var VersionInfo
     */
    private VersionInfo $versionInfo;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param VersionInfo $versionInfo
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        VersionInfo $versionInfo
    ) {
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->versionInfo = $versionInfo;
    }

    /**
     * Getting back the reference text
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(AbstractElement $element)
    {
        $docsUrl = 'https://docs.klarna.com/platform-solutions/adobe-commerce';
        $logsUrl = $this->urlBuilder->getUrl('klarna/index/logs');
        $supportUrl = $this->urlBuilder->getUrl('klarna_support/index/support/form/new');

        $translatedString = __('here');

        return
            __('Plugin version: ') .
            $this->versionInfo->getM2KlarnaVersion() . '<br/><br/>' .
            __('Documentation can be found') .
            " <p style='display:inline'><a href='$docsUrl' target='_blank'>$translatedString</a></p>, " .
            __('logs can be found') .
            " <p style='display:inline'><a href='$logsUrl' target='_blank'>$translatedString</a></p>. " .
            __('You can report an issue or ask a question') .
            " <p style='display:inline'><a href='$supportUrl' target='_blank'>$translatedString</a></p>.";
    }
}
