<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ReadMore\Plugin;

use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use voku\helper\HtmlDomParser;

/**
 * Class RenderReadMore
 */
class RenderReadMore
{
    /**
     * MergeJson constructor.
     *
     * @param RequestHttp $request
     */
    public function __construct(
        RequestHttp $request
    ) {
        $this->request = $request;
    }

    /**
     * @param ResultInterface $subject
     * @param \Closure $proceed
     * @param ResponseInterface $response
     *
     * @return string
     */
    public function aroundRenderResult(
        ResultInterface $subject,
        \Closure $proceed,
        ResponseInterface $response
    ) {
        $result = $proceed($response);

        if (PHP_SAPI === 'cli' || $this->request->isXmlHttpRequest() ||
            $this->request->getFullActionName() == 'checkout_klarna_index'
            || $this->request->getFullActionName() == '__')
        {
            return $result;
        }

        $html = $response->getBody();

        $dom = new HtmlDomParser();
        $dom = $dom
            ->loadHtml($html);

        $elements = $dom->findMultiOrFalse('readmore[data-mage-init]');

        if($elements === false) {
            return $result;
        }

        foreach ($elements as $element) {
            $data = json_decode($element->getAttribute('data-mage-init'), true);
            $closestParent = $element;
            $closestClass = str_replace('.', '', $data['readMoreWidget']['element']);

            while ($parent = $closestParent->parent()) {
                if (strpos($parent->getAttribute('class'), $closestClass) !== false) {
                    break;
                } elseif ($parent->tag !== '#document') {
                    $closestParent = $parent;
                } else {
                    break;
                }
            }
            $allow = false;
            foreach ($parent->children() as $children) {
                if ($children->tag === 'readmore' || $children->findOneOrFalse('readmore[data-mage-init]')) {
                    $allow = true;
                    continue;
                } else if($children->outerhtml != '' && $allow) {
                    $children->plaintext = '<span class="hide-on-load-readmore">' . $children->html() . '</span>';
                }
            }
        }

        $response->setBody($dom);

        return $result;
    }
}