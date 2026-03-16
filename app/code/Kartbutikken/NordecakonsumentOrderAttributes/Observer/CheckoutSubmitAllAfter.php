<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\NordecakonsumentOrderAttributes\Observer;

use Hryvinskyi\Base\Helper\ArrayHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CheckoutSubmitAllAfter
 */
class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * BeforeOrderPaymentSaveObserver constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Sets current instructions for bank transfer account
     *
     * @param Observer $observer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $params = $this->request->getParams();

        $order->setData('use_invoice_email', ArrayHelper::getValue($params, 'payment.use_invoice_email'));
        $order->setData('invoice_email', ArrayHelper::getValue($params, 'payment.invoice_email'));
        $order->setData('reference_code', ArrayHelper::getValue($params, 'payment.reference_code'));
        $order->save();
    }
}