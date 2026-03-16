<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\OptimizeXMagentoInit\Plugin;

use Closure;
use Hryvinskyi\Base\Helper\ArrayHelper;
use Hryvinskyi\Base\Helper\Json;
use Hryvinskyi\Base\Helper\VarDumper;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class MergeJson
 */
class MergeJson
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
     * @param Closure $proceed
     * @param Http $response
     *
     * @return string
     */
    public function aroundRenderResult(
        ResultInterface $subject,
        Closure $proceed,
        ResponseInterface $response
    ) {
        $result = $proceed($response);


        $urlFastOrder = $this->request->getRequestUri();
        $urlFastOrder = strtok($urlFastOrder, '?');
        if (PHP_SAPI === 'cli' || $this->request->isXmlHttpRequest() ||
            $this->request->getFullActionName() == 'checkout_klarna_index'
            || $this->request->getFullActionName() == 'fastorder_index_add'
            || $urlFastOrder == '/fast-order'
            || $urlFastOrder == '/fast-order2'
            || $this->request->getFullActionName() == '__') {
            return $result;
        }

        $jsons = [];
        $html = $response->getBody();

        $scriptStart = '<script';
        $scriptEnd = '</script>';

        $start = 0;
        $i = 0;
        while (($start = stripos($html, $scriptStart, $start)) !== false) {
            $end = stripos($html, $scriptEnd, $start);

            if ($end === false) {
                break;
            }

            $len = $end + strlen($scriptEnd) - $start;
            $script = substr($html, $start, $len);
            if(strpos($script, 'text/x-magento-init') !== false) {
                $jsons[] = strip_tags($script);
                $html = str_replace($script, '', $html);
            } else {
                $start++;
                continue;
            }

            $i++;
        }
        $merged = [];
        foreach ($jsons as $json) {
            $json = Json::decode($json);
            $merged = ArrayHelper::merge($merged, $json);
        }

        if (count($merged) > 0) {
            $merged = '<script type=text/x-magento-init>' . Json::encode($merged) . '</script>';
        } else {
            $merged = '';
        }

        if ($endBody = stripos($html, '</body>')) {
            $html = substr($html, 0, $endBody) . $merged . substr($html, $endBody);
        } else {
            $html .= $merged;
        }

        $response->setBody($html);

        return $result;
    }
}
