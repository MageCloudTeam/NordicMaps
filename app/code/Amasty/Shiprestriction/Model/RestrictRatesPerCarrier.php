<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model;

use Amasty\Shiprestriction\Model\Message\RestrictionMessageProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Rate\CarrierResult;

class RestrictRatesPerCarrier
{
    /**
     * @var ErrorFactory
     */
    private $rateErrorFactory;

    /**
     * @var CanShowMessageOnce
     */
    private $canShowMessageOnce;

    /**
     * @var RestrictionMessageProcessor
     */
    private $messageProcessor;

    public function __construct(
        ErrorFactory $rateErrorFactory,
        CanShowMessageOnce $canShowMessageOnce,
        RestrictionMessageProcessor $messageProcessor = null // TODO move to not optional
    ) {
        $this->rateErrorFactory = $rateErrorFactory;
        $this->canShowMessageOnce = $canShowMessageOnce;
        $this->messageProcessor = $messageProcessor
            ?? ObjectManager::getInstance()->get(RestrictionMessageProcessor::class);
    }

    /**
     * @param CarrierResult $result
     * @param string $carrierCode
     * @param Method[] $carrierRates
     * @param Rule[] $rules
     * @return void
     */
    public function execute(
        CarrierResult $result,
        string $carrierCode,
        array $carrierRates,
        array $rules
    ): void {
        foreach ($carrierRates as $rate) {
            $restrict = false;

            foreach ($rules as $rule) {
                if ($rule->match($rate)) {
                    $restrict = true;

                    $message = false;
                    // collect amstrates methods to one string in case "Show Restriction Message Once" = Yes
                    if ($rate->getCarrier() === 'amstrates'
                        && $this->canShowMessageOnce->execute($rule, $carrierCode)
                    ) {
                        $methodTitles[] = $rate->getMethodTitle();
                        // all rate method_title(s) are collected and ready to be added to the final message
                        // after the last rate is processed.
                        if ($rate === $carrierRates[array_key_last($carrierRates)]) {
                            $rate->setData('method_title', implode(', ', $methodTitles));
                            $message = $this->messageProcessor->process($rate, $rule);
                        }
                    } else {
                        $message = $this->messageProcessor->process($rate, $rule);
                    }

                    if ($message) {
                        if ($rate instanceof Error) {
                            $rate->setErrorMessage($message);
                            $result->append($rate);
                        } else {
                            $this->appendError($result, $rate, $message);
                        }

                        if ($this->canShowMessageOnce->execute($rule, $carrierCode)) {
                            return;
                        }

                        break;
                    }
                }
            }

            if (!$restrict) {
                $result->append($rate);
            }
        }
    }

    private function appendError(CarrierResult $result, Method $rate, string $errorMessage): void
    {
        /** @var Error $error */
        $error = $this->rateErrorFactory->create();
        $error->setData('carrier', $rate->getData('carrier'));
        $error->setData('carrier_title', $rate->getData('carrier_title'));
        $error->setData('error_message', $errorMessage);

        $result->append($error);
    }
}
