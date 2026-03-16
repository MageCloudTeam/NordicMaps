<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Integration\Controller\Klarna;

use Klarna\Base\Test\Integration\Helper\ControllerTestCase;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Url;
use Klarna\Kp\Model\Quote;

/**
 * @internal
 */
class CookieTest extends ControllerTestCase
{

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoMagentoQuoteWasFoundInTheKlarnaTableImpliesReturnsDefaultUrl(): void
    {
        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoActiveKlarnaQuoteFoundImpliedReturnsDefaultUrl(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $this->session->setLastQuoteId($magentoQuote->getId());

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoRedirectUrlIsAddedToTheKlarnaQuoteImpliedReturnsDefaultUrl(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $this->session->setLastQuoteId($magentoQuote->getId());

        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertEquals('checkout/onepage/success/', $urlInstance->getData()['route_path']);
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteRedirectUrlIsReturned(): void
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();
        $this->session->setLastQuoteId($magentoQuote->getId());

        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setRedirectUrl('http://example.com');
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        $this->sendRequest(
            [],
            'checkout/klarna/cookie',
            Http::METHOD_GET
        );

        $urlInstance = $this->getUrlInstance();
        static::assertTrue(!isset($urlInstance->getData()['route_path']));
    }

    /**
     * Getting back the URL instance
     *
     * @return Url
     */
    private function getUrlInstance(): Url
    {
        return $this->objectManager->get(Url::class);
    }
}
