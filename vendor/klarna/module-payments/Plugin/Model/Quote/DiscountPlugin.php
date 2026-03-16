<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Plugin\Model\Quote;

use Klarna\Kp\Model\Configuration\ApiValidation;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\SalesRule\Model\Quote\Discount;
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;

/**
 * @internal
 */
class DiscountPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var KlarnaSession
     */
    private KlarnaSession $klarnaSession;
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;

    /**
     * @param RequestInterface $request
     * @param KlarnaSession $klarnaSession
     * @param ApiValidation $apiValidation
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        KlarnaSession $klarnaSession,
        ApiValidation $apiValidation
    ) {
        $this->request = $request;
        $this->klarnaSession = $klarnaSession;
        $this->apiValidation = $apiValidation;
    }

    /**
     * Sets the payment method in the address if it is set in the request
     *
     * @param Discount                    $subject
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total                       $total
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function beforeCollect(
        Discount $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): array {
        if (!$this->apiValidation->isKpEnabled($quote->getStore())) {
            return [$quote, $shippingAssignment, $total];
        }

        $address = $shippingAssignment->getShipping()->getAddress();
        $paymentMethod = $this->getPaymentMethodFromRequest();

        if (!$address->getPaymentMethod()
            && $paymentMethod
            && in_array($paymentMethod, $this->klarnaSession->getPaymentMethods())) {
            $address->setPaymentMethod($paymentMethod);
        }

        return [$quote, $shippingAssignment, $total];
    }

    /**
     * Gets the payment method from the request
     *
     * @return string|null
     */
    private function getPaymentMethodFromRequest(): ?string
    {
        $content = json_decode($this->request->getContent(), true);
        if (isset($content['paymentMethod']['method'])) {
            return $content['paymentMethod']['method'];
        }
        return null;
    }
}
