<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Backend\Api\ApiInterface;
use Klarna\Kp\Model\Configuration\Payment;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;

/**
 * @internal
 */
class OrderUpdate
{
    /**
     * @var ApiInterface
     */
    private ApiInterface $orderManagementApi;
    /**
     * @var OrderAddressRepositoryInterface
     */
    private OrderAddressRepositoryInterface $magentoOrderAddressRepository;
    /**
     * @var Payment
     */
    private Payment $paymentConfiguration;

    /**
     * @param ApiInterface $orderManagementApi
     * @param OrderAddressRepositoryInterface $magentoOrderAddressRepository
     * @param Payment $paymentConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        ApiInterface $orderManagementApi,
        OrderAddressRepositoryInterface $magentoOrderAddressRepository,
        Payment $paymentConfiguration
    ) {
        $this->orderManagementApi = $orderManagementApi;
        $this->magentoOrderAddressRepository = $magentoOrderAddressRepository;
        $this->paymentConfiguration = $paymentConfiguration;
    }

    /**
     * Updating the order addresses
     *
     * @param string $klarnaOrderId
     * @param OrderInterface $order
     */
    public function updateAddresses(string $klarnaOrderId, OrderInterface $order): void
    {
        if (!$this->paymentConfiguration->isB2bEnabled($order->getStore())) {
            return;
        }

        $klarna = $this->orderManagementApi->getPlacedKlarnaOrder($klarnaOrderId);
        foreach ($order->getAddresses() as $address) {
            $usedKlarnaAddress = $klarna->getBillingAddress();

            if ($address->getAddressType() === 'shipping') {
                $usedKlarnaAddress = $klarna->getShippingAddress();
            }
            $address->setStreet($usedKlarnaAddress['street_address']);
            $address->setPostcode($usedKlarnaAddress['postal_code']);
            $address->setCity($usedKlarnaAddress['city']);

            $this->magentoOrderAddressRepository->save($address);
        }
    }
}
