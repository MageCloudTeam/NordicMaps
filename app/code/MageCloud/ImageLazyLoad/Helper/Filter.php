<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ImageLazyLoad\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;

class Filter extends AbstractHelper
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Filter constructor.
     *
     * @param Context $context
     * @param Config $config
     */
    public function __construct(Context $context, Config $config)
    {
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * Convert content to lazyload html
     *
     * @param string $content
     *
     * @return string
     */
    public function filter($content)
    {
        if ($this->_getRequest()->isAjax()) {
            return $content;
        }

        $matches = $search = $replace = [];
        preg_match_all('/<img[\s\r\n]+.*?>/is', $content, $matches);
        $regex = '/<img([^<]+\s|\s)src=(\"|' . "\')([^<]+\.(png|jpg|jpeg))[^<]+>(?!(<\/pic|\s*<\/pic))/mi";

        $lazyClasses = 'lazy lazy-loading';

        foreach ($matches[0] as $imgHTML) {
            if (!preg_match("/src=['\"]data:image/is", $imgHTML) && !$this->isSkipElement($imgHTML)) {
                preg_match_all($regex, $imgHTML, $images, PREG_OFFSET_CAPTURE);

                if (isset($images[3][0][0])) {
                    $width = 1;
                    $height = 1;
                    $placeHolderUrl = 'data:image/svg+xml;base64,';

                    $image = ObjectManager::getInstance()->get(\Jajuma\WebpImages\Helper\Data::class)->getImagePathFromUrl($images[3][0][0]);

                    if(!is_bool($image) && @($size = getimagesize($image)) !== false) {
                        [$width, $height, $type, $attr] = $size;
                    }

                    $placeHolderSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '"><rect width="100%" height="100%" fill="#f1f1f1" fill-opacity="0.25" /></svg>';
                    $placeHolderUrl .= base64_encode($placeHolderSVG);
                } else {
                    $placeHolderUrl = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
                }

                // replace the src and add the data-src attribute
                $replaceHTML = preg_replace('/<img(.*?)src=/is', '<img$1src="' . $placeHolderUrl . '" data-src=', $imgHTML);

                // add the lazy class to the img element
                if (preg_match('/class=["\']/i', $replaceHTML)) {
                    $replaceHTML = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1', $replaceHTML);
                } else {
                    $replaceHTML = preg_replace('/<img/is', '<img class="' . $lazyClasses . '"', $replaceHTML);
                }

                $search[] = $imgHTML;
                $replace[] = $replaceHTML;
            }
        }

        $content = str_replace($search, $replace, $content);

        $content = preg_replace('/<iframe(.*?) src=(".*?")(.*?)<\/iframe>/', '<iframe$1 data-src=$2 class="lazy" $3</iframe>', $content);

        return $content;
    }

    /**
     * Check is skip element via specific classes
     *
     * @param string $content
     *
     * @return boolean
     */
    protected function isSkipElement($content)
    {
        $skipClassesQuoted = array_map('preg_quote', $this->getSkipClasses());
        $skipClassesORed = implode('|', $skipClassesQuoted);
        $regex = '/<\s*\w*\s*class\s*=\s*[\'"](|.*\s)' . $skipClassesORed . '(|\s.*)[\'"].*>/isU';

        return preg_match($regex, $content) || preg_match('/\s*\w*\s*<%/isU', $content);
    }

    /**
     * @return array
     */
    protected function getSkipClasses()
    {
        $skipClasses = array_map('trim', explode(',', $this->config->getSkipClasses()));

        foreach ($skipClasses as $k => $_class) {
            if (!$_class) {
                unset($skipClasses[$k]);
            }
        }

        return $skipClasses;
    }
}
