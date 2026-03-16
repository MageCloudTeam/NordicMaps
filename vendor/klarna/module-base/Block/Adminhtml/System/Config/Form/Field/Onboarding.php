<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Getting back the Klarna Merchant Onboarding text with link
 *
 * @internal
 */
class Onboarding extends Field
{
    public const URL = 'https://portal.klarna.com/signup';

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function render(AbstractElement $element)
    {
        $urlText = __('link');
        $urlTag = '<p style="display:inline"><a href="' . self::URL . '" target="_blank">' . $urlText . '</a></span>';

        return  __('Click on this %1 to visit the Klarna Merchant Onboarding Page and request credentials.', $urlTag);
    }
}
