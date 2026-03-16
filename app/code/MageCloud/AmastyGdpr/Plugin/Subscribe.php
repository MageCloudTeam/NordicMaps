<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AmastyGdpr\Plugin;

use Amasty\Gdpr\Block\Checkbox;
use Amasty\Gdpr\Model\ConsentLogger;
use Magento\Newsletter\Block\Subscribe as SubscribeBlock;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Subscribe
 */
class Subscribe
{
    /**
     * @param SubscribeBlock $subject
     * @param $result
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(SubscribeBlock $subject, $result)
    {
        $layout = $subject->getLayout();

        if (!$layout->getBlock('form.subscribe')
            || $layout->getBlock('amasty_gdpr_newsletter')
        ) {
            return $result;
        }

        $checkboxBlock = $layout->createBlock(
            Checkbox::class,
            'amasty_gdpr_newsletter',
            [
                'scope' => ConsentLogger::FROM_SUBSCRIPTION
            ]
        )->setTemplate('Amasty_Gdpr::checkbox.phtml')->toHtml();
        if ($checkboxBlock) {
            $pos = strripos($result, '<div class="actions">');
            $endOfHtml = substr($result, $pos);
            $result = substr_replace($result, $checkboxBlock, $pos) . $endOfHtml;
        }

        return $result;
    }
}
