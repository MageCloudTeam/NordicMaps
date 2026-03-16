<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Assignment\Helper;

use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Klarna\Kss\Model\Carrier;
use Magento\Tax\Api\Data\AppliedTaxInterface;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;

/**
 * @api
 */
class AppliedShippingTaxFactory
{
    /**
     * @var AppliedTaxInterfaceFactory
     */
    private AppliedTaxInterfaceFactory $appliedTaxFactory;
    /**
     * @var AppliedTaxRateInterfaceFactory
     */
    private AppliedTaxRateInterfaceFactory $appliedTaxRateFactory;

    /**
     * @param AppliedTaxInterfaceFactory     $appliedTaxFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        AppliedTaxInterfaceFactory $appliedTaxFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateFactory
    ) {
        $this->appliedTaxFactory = $appliedTaxFactory;
        $this->appliedTaxRateFactory = $appliedTaxRateFactory;
    }

    /**
     * Getting back the AppliedTax data object based on applied tax rate and tax amount
     *
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @return AppliedTaxInterface
     */
    public function getAppliedTax(ShippingMethodGatewayInterface $shippingMethodGateway): AppliedTaxInterface
    {
        $appliedTaxDataObject = $this->appliedTaxFactory->create();
        $appliedTaxDataObject->setAmount($shippingMethodGateway->getTaxAmount());
        $appliedTaxDataObject->setPercent($shippingMethodGateway->getTaxRate());
        $appliedTaxDataObject->setTaxRateKey(Carrier::GATEWAY_KEY);

        $rateDataObjects = [
            'Klarna shipping tax' => $this->appliedTaxRateFactory->create()
        ];
        $rateDataObjects['Klarna shipping tax']->setPercent($shippingMethodGateway->getTaxRate());
        $rateDataObjects['Klarna shipping tax']->setCode('Klarna shipping tax');
        $rateDataObjects['Klarna shipping tax']->setTitle(__('Shipping tax'));

        $appliedTaxDataObject->setRates($rateDataObjects);
        return $appliedTaxDataObject;
    }
}
