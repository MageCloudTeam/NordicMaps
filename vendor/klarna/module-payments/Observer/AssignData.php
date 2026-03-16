<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Observer;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\PaymentMethods\AdditionalInformationUpdater;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class AssignData extends AbstractDataAssignObserver
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var AdditionalInformationUpdater
     */
    private AdditionalInformationUpdater $additionalInformationUpdater;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param AdditionalInformationUpdater $additionalInformationUpdater
     * @param ApiValidation $apiValidation
     * @codeCoverageIgnore
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        AdditionalInformationUpdater $additionalInformationUpdater,
        ApiValidation $apiValidation
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->additionalInformationUpdater = $additionalInformationUpdater;
        $this->apiValidation = $apiValidation;
    }

    /**
     * Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additionalDataFlat = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        $additionalData = $this->dataObjectFactory->create(['data' => $additionalDataFlat]);

        $payment = $this->readPaymentModelArgument($observer);
        $quote = $payment->getQuote();
        if (!$this->apiValidation->isKpEnabled($quote->getStore())) {
            return;
        }

        $this->additionalInformationUpdater->updateByInput($additionalData, $payment);
    }
}
