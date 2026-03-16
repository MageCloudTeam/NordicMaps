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
use Klarna\Kp\Model\Quote;
use Klarna\Kp\Model\QuoteRepository;
use Magento\Framework\App\Request\Http;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class AuthorizationTokenUpdateTest extends ControllerTestCase
{

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteNoAuthorizationTokenGivenImpliesNoKlarnaQuoteSaveOperation(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            [],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(400, $result['statusCode']);
        static::assertEmpty($klarnaQuote->getAuthorizationToken());
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteEmptyAuthorizationTokenGivenImpliesNoKlarnaQuoteSaveOperation(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            ['authorization_token' => ''],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(400, $result['statusCode']);
        static::assertEmpty($klarnaQuote->getAuthorizationToken());
    }

    /**
     * @magentoConfigFixture current_store payment/klarna_kp/active 1
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExecuteValidAuthorizationTokenGivenImpliesKlarnaQuoteSaveOperation(): void
    {
        $magentoQuote = $this->init();

        $result = $this->sendRequest(
            ['authorization_token' => 'abc'],
            'checkout/klarna/authorizationTokenUpdate',
            Http::METHOD_PUT
        );

        $klarnaQuote = $this->getKlarnaQuote($magentoQuote);
        static::assertEquals(200, $result['statusCode']);
        static::assertEquals('abc', $klarnaQuote->getAuthorizationToken());
    }

    private function init(): CartInterface
    {
        $magentoQuote = $this->session->getQuote();
        $magentoQuote->save();

        $klarnaQuote = $this->objectManager->get(Quote::class);
        $klarnaQuote->setQuoteId($magentoQuote->getId());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->save();

        return $magentoQuote;
    }

    private function getKlarnaQuote(CartInterface $magentoQuote): Quote
    {
        $klarnaRepository = $this->objectManager->get(QuoteRepository::class);
        return $klarnaRepository->getActiveByQuote($magentoQuote);
    }
}
