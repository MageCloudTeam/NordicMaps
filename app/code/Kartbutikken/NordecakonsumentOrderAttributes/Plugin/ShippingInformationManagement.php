<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\NordecakonsumentOrderAttributes\Plugin;

use Closure;
use Hryvinskyi\Base\Helper\VarDumper;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ShippingInformationManagement
 */
class ShippingInformationManagement
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * PaymentInformationManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param PaymentInformationManagement $subject
     * @param Closure $proceed
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagement $subject,
        Closure $proceed,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($result = $proceed($cartId, $paymentMethod, $billingAddress)) {
            $additionalData = $this->filterAdditionalData($paymentMethod->getAdditionalData());
            if (count($additionalData) > 0) {
                $order = $this->orderRepository->get($result);
                $quote = $this->quoteRepository->get($order->getQuoteId());

                $this->saveQuote($quote, $additionalData);
                $this->saveOrder($order, $additionalData);
            }
        }

        return $result;
    }

    /**
     * @param PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($paymentMethod->getAdditionalData()) {
            $additionalData = $this->filterAdditionalData($paymentMethod->getAdditionalData());
        } else {
            $additionalData = array();
        }
        if (count($additionalData) > 0) {
            $quote = $this->quoteRepository->get($cartId);
            $this->saveQuote($quote, $additionalData);
        }
    }

    /**
     * @param CartInterface $quote
     * @param array $additionalData
     */
    private function saveQuote(CartInterface $quote, array $additionalData): void
    {
        foreach ($additionalData as $key => $data) {
            $quote->setData($key, $data);
        }

        $quote->save();
    }

    /**
     * @param OrderInterface $order
     * @param array $additionalData
     */
    private function saveOrder(OrderInterface $order, array $additionalData): void
    {
        foreach ($additionalData as $key => $data) {
            $order->setData($key, $data);
        }

        $order->save();
    }

    /**
     * Return filtered additional data
     *
     * @param array $additionalData
     *
     * @return array
     */
    public function filterAdditionalData(array $additionalData): array
    {
        return array_intersect_key($additionalData, array_fill_keys($this->allowedAdditionalData(), '')) ?? [];
    }

    /**
     * Allowed keys to save in order or quote
     *
     * @return array
     */
    public function allowedAdditionalData(): array
    {
        return [
            'use_invoice_email',
            'invoice_email',
            'reference_code'
        ];
    }
}
