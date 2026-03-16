<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http;
use Magento\Store\Model\ScopeInterface;

/**
 * Class JsFooter
 */
class JsFooter
{
    private const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Put all javascript to footer before sending the response.
     *
     * @param Http $subject
     * @return void
     */
    public function beforeSendResponse(Http $subject)
    {
        $content = (string)$subject->getContent();
        $script = [];
        if (strpos($content, '</body') !== false) {
            if ($this->scopeConfig->isSetFlag(
                self::XML_PATH_DEV_MOVE_JS_TO_BOTTOM,
                ScopeInterface::SCOPE_STORE
            )
            ) {
                $pattern = '#<script[^>]*+(?<!text/x-magento-template.)>.*?</script>#is';
                $content = preg_replace_callback(
                    $pattern,
                    function ($matchPart) use (&$script) {
                        $script[] = $matchPart[0];
                        return '';
                    },
                    $content
                );
                $subject->setContent(
                    str_replace('</body', implode("\n", $script) . "\n</body", $content)
                );
            }
        }
    }
}