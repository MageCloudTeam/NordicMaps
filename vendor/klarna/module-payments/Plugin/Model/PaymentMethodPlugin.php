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
use Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\PaymentMethod;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class PaymentMethodPlugin
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
     * @param PaymentMethod $method
     * @param array         $result
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGenerateFilterText($method, array $result): array
    {
        try {
            $magentoQuoteId = $this->klarnaSession->getMagentoQuoteId();
            $magentoQuote = $this->magentoQuoteRepository->get($magentoQuoteId);
        } catch (NoSuchEntityException $e) {
            return $result;
        }

        if (!$this->apiValidation->isKpEnabled($magentoQuote->getStore())) {
            return $result;
        }

        $handledFilterTextParts = [];
        foreach ($result as $filterTextPart) {
            if ($this->isPaymentMethod($filterTextPart)) {
                $filterTextPart = $this->replacePaymentMethod($filterTextPart);
            }
            $handledFilterTextParts[] = $filterTextPart;
        }
        return $handledFilterTextParts;
    }

    /**
     * Checks if input is a payment method
     *
     * @param string $input
     * @return bool
     */
    private function isPaymentMethod(string $input): bool
    {
        return strpos($input, 'quote_address:payment_method') === 0;
    }

    /**
     * Replaces payment methods saved in klarna quote with the kp key
     *
     * @param string $input
     * @return string
     */
    private function replacePaymentMethod(string $input): string
    {
        return str_replace(
            $this->klarnaSession->getPaymentMethods(),
            Kp::METHOD_CODE,
            $input
        );
    }
}
