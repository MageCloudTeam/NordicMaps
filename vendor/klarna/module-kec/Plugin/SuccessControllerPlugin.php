<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;
use Klarna\Kec\Model\Session as KecSession;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Checkout\Controller\Onepage\Success;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Klarna\Logger\Api\LoggerInterface;

/**
 * @internal
 */
class SuccessControllerPlugin
{
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var KecSession
     */
    private KecSession $kecSession;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CheckoutSession $checkoutSession
     * @param KecSession $kecSession
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        KecSession $kecSession,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->kecSession = $kecSession;
        $this->logger = $logger;
    }

    /**
     * Adding back the current quote to the session
     *
     * @param Success $success
     * @param Page|Redirect $result
     * @return Page
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(Success $success, $result)
    {
        if ($this->kecSession->entryExists()) {
            $quoteIdOld = $this->kecSession->getOldQuoteId();
            $this->logger->info(
                'Success page: After creating the result page object using the original quote with ID ' .
                $quoteIdOld
            );

            $this->checkoutSession->setQuoteId($quoteIdOld);
        }

        return $result;
    }
}
