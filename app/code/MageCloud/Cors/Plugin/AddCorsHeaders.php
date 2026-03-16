<?php

namespace MageCloud\Cors\Plugin;

use Magento\Framework\App\Response\Http as HttpResponse;

class AddCorsHeaders
{
    public function beforeSendResponse(HttpResponse $subject)
    {
        $subject->setHeader('Access-Control-Allow-Origin', 'https://admi.nordicmaps.no', true);
        $subject->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PUT', true);
        $subject->setHeader('Access-Control-Allow-Headers', 'x-requested-with, Content-Type, Authorization, X-Requested-With', true);
    }
}
