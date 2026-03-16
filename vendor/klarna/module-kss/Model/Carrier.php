<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory as RateResultFactory;

/**
 * Returning values for the Klarna Shipping Carrier instance
 *
 * @internal
 */
class Carrier
{
    public const GATEWAY_KEY = 'klarna_shipping_method_gateway';
    public const CODE        = 'klarna_shipping_method_gateway';
    public const TITLE       = 'Klarna shipping method gateway';

    /**
     * @var RateResultFactory
     */
    private $rateFactory;
    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @param RateResultFactory $rateFactory
     * @param MethodFactory     $rateMethodFactory
     * @codeCoverageIgnore
     */
    public function __construct(RateResultFactory $rateFactory, MethodFactory $rateMethodFactory)
    {
        $this->rateFactory = $rateFactory;
        $this->rateMethodFactory = $rateMethodFactory;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest                    $request
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collectRates(RateRequest $request, ShippingMethodGatewayInterface $shippingMethodGateway): Result
    {
        $result = $this->rateFactory->create();
        $method = $this->createResultMethod($shippingMethodGateway);
        $result->append($method);
        return $result;
    }

    /**
     * Creating the result method
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @return Method
     */
    private function createResultMethod(ShippingMethodGatewayInterface $shippingMethodGateway): Method
    {
        /** @var Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier('klarna');
        $method->setCarrierTitle('Klarna');

        $method->setMethod('shipping_method_gateway');

        $methodTitle = self::TITLE;
        if (!empty($shippingMethodGateway->getName())) {
            $methodTitle = $shippingMethodGateway->getName();
        }

        $method->setMethodTitle($methodTitle);

        if ($shippingMethodGateway->isPickUpPoint()) {
            $suffixName = ' (' . $shippingMethodGateway->getPickUpPointName() . ')';
            $method->setMethodTitle(self::TITLE . ' ' . $suffixName);
        }
        $method->setPrice($shippingMethodGateway->getShippingAmount());
        $method->setAmount($shippingMethodGateway->getShippingAmount());
        $method->setCost($shippingMethodGateway->getShippingAmount());
        return $method;
    }

    /**
     * Returns true if the request consist of valid values
     *
     * @param DataObject $request
     * @return bool
     */
    public function isValidRequest(DataObject $request): bool
    {
        return !empty($request->getAllItems());
    }
}
