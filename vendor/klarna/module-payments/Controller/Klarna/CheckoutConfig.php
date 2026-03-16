<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Base\Model\Responder\Result;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;

/**
 * @api
 */
class CheckoutConfig implements HttpPostActionInterface
{
    /**
     * @var CompositeConfigProvider
     */
    private CompositeConfigProvider $compositeConfigProvider;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param CompositeConfigProvider $compositeConfigProvider
     * @param Result $result
     * @param Session $session
     * @param RequestInterface $request
     * @codeCoverageIgnore
     */
    public function __construct(
        CompositeConfigProvider $compositeConfigProvider,
        Result $result,
        Session $session,
        RequestInterface $request
    ) {
        $this->compositeConfigProvider = $compositeConfigProvider;
        $this->result = $result;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * Setting the shipping country, recalculating the checkout configuration and returning it.
     */
    public function execute()
    {
        $rawParameter = $this->request->getContent();
        $parameter = json_decode($rawParameter, true);
        $quote = $this->session->getQuote();

        if (isset($parameter['shipping_country_id'])) {
            $quote->getShippingAddress()
                ->setCountryId($parameter['shipping_country_id']);
        }
        if (isset($parameter['shipping_company'])) {
            $quote->getShippingAddress()
                ->setCompany($parameter['shipping_company']);
        }
        if (isset($parameter['billing_country_id'])) {
            $quote->getBillingAddress()
                ->setCountryId($parameter['billing_country_id']);
        }
        if (isset($parameter['billing_company'])) {
            $quote->getBillingAddress()
                ->setCompany($parameter['billing_company']);
        }

        $result = $this->compositeConfigProvider->getConfig();
        return $this->result->getJsonResult(200, $result);
    }
}
