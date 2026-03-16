<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\QuoteRepository;
use Klarna\Logger\Model\Logger;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
class Cookie implements HttpGetActionInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var DefaultConfigProvider
     */
    private $defaultConfigProvider;
    /**
     * @var RedirectFactory
     */
    private RedirectFactory $redirectFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param Session $session
     * @param UrlInterface $urlBuilder
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param RedirectFactory $redirectFactory
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Logger $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        UrlInterface $urlBuilder,
        DefaultConfigProvider $defaultConfigProvider,
        RedirectFactory $redirectFactory,
        QuoteRepositoryInterface $klarnaQuoteRepository = null,
        Logger $logger = null,
    ) {
        $this->checkoutSession = $session;
        $this->urlBuilder = $urlBuilder;
        $this->defaultConfigProvider = $defaultConfigProvider;
        $this->redirectFactory = $redirectFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository ?: ObjectManager::getInstance()->get(
            QuoteRepository::class
        );
        $this->logger = $logger ?: ObjectManager::getInstance()->get(
            Logger::class
        );
    }

    /**
     * Redirecting the customer to a url to set the cookie.
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        return $this->redirectFactory->create()->setPath($this->getRedirectUrl());
    }

    /**
     * Retrieve the redirect url, that was set to the checkout session during the authorize
     *
     * @return string
     */
    private function getRedirectUrl(): string
    {
        $quoteId = $this->checkoutSession->getLastQuoteId();
        if ($quoteId === null) {
            $this->logger->forceLogging(
                'No Klarna redirect URL could be used because no final Magento quote ID is added ' .
                'to the checkout session'
            );
            return $this->getSuccessPageUrl();
        }

        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuoteId($quoteId);
        } catch (NoSuchEntityException $e) {
            $this->logger->forceLogging(
                'No Klarna redirect URL could be used because there is no ' .
                'active Klarna quote for the Magento quote ID: ' .
                $quoteId
            );
            $this->logger->logException($e);
            return $this->getSuccessPageUrl();
        }

        $redirectUrl = $klarnaQuote->getRedirectUrl();

        if (!$redirectUrl) {
            $this->logger->forceLogging(
                'No Klarna redirect URL could be used because there is no ' .
                'redirect URL set for the Klarna quote ID: ' .
                $klarnaQuote->getId()
            );
            return $this->getSuccessPageUrl();
        }
        return $redirectUrl;
    }

    /**
     * Getting back the success page url
     *
     * @return string
     */
    private function getSuccessPageUrl(): string
    {
        return $this->urlBuilder->getUrl($this->defaultConfigProvider->getDefaultSuccessPageUrl());
    }
}
