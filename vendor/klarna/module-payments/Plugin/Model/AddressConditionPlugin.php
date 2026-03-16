<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Plugin\Model;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Payment\Kp;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class AddressConditionPlugin
{
    /**
     * @var KlarnaSession
     */
    private KlarnaSession $klarnaSession;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;

    /**
     * @param KlarnaSession $klarnaSession
     * @param ApiValidation $apiValidation
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaSession $klarnaSession,
        ApiValidation $apiValidation,
        CartRepositoryInterface $magentoQuoteRepository
    ) {
        $this->klarnaSession = $klarnaSession;
        $this->apiValidation = $apiValidation;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
    }

    /**
     * Replaces detailed payment method names with generic kp key
     *
     * @param Address $address
     * @param mixed $validatedValue
     * @return mixed
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function beforeValidateAttribute(Address $address, $validatedValue)
    {
        if ($validatedValue === null) {
            return $validatedValue;
        }
        if (!is_array($validatedValue)) {
            $validatedValue = (string) $validatedValue;
        }

        try {
            $magentoQuoteId = $this->klarnaSession->getMagentoQuoteId();
            $magentoQuote = $this->magentoQuoteRepository->get($magentoQuoteId);
        } catch (NoSuchEntityException $e) {
            return $validatedValue;
        }

        if ($this->apiValidation->isKpEnabled($magentoQuote->getStore())) {
            $validatedTrimmed = str_replace('klarna_', '', $validatedValue);
            $paymentMethods = $this->klarnaSession->getPaymentMethodInformation();
            foreach ($paymentMethods as $paymentMethod) {
                if (in_array($validatedTrimmed, $paymentMethod)) {
                    return Kp::METHOD_CODE;
                }
            }
        }

        return $validatedValue;
    }
}
