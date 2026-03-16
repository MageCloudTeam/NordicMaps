<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Placement\AuthorizationCallback;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Api\QuoteAuthCallbackTokenInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    /**
     * @var RequestValidator
     */
    private RequestValidator $requestValidator;
    /**
     * @var array|MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var TestObjectFactory
     */
    private TestObjectFactory $objectFactory;

    /**
     * @return void
     */
    public function testValidateRequestBodyThrowsNoExceptionWithCorrectData(): void
    {
        $this->dependencyMocks['request']
            ->method('getContent')
            ->willReturn(json_encode([
                'session_id' => 'a-session-id',
                'authorization_token' => 'a-authorization-token',
            ]));

        try {
            $this->requestValidator->validateRequestBody();
        } catch (LocalizedException $exception) {
            // If an exception is caught, the test will fail
            $this->fail('validateRequestBody threw an exception: ' . $exception->getMessage());
        }

        $this->expectNotToPerformAssertions();
    }

    /**
     * @dataProvider requestBodyData
     *
     * @param string $requestContent
     * @param string $exceptionMessage
     * @return void
     * @throws LocalizedException
     */
    public function testValidateRequestBodyMethodReturnsExceptionOnInvalidData(
        string $requestContent,
        string $exceptionMessage
    ): void {
        $this->dependencyMocks['request']
            ->method('getContent')
            ->willReturn($requestContent);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->requestValidator->validateRequestBody();
    }

    /**
     * @return void
     */
    public function testVerifyAuthCallbackTokenShouldNotThrowExceptionIfAuthCallbackTokenIsValid(): void
    {
        $this->dependencyMocks['request']
            ->method('getParam')
            ->willReturn('correct-token');

        $klarnaQuote = $this->getMockForAbstractClass(QuoteAuthCallbackTokenInterface::class);
        $klarnaQuote
            ->method('getAuthTokenCallbackToken')
            ->willReturn('correct-token');

        try {
            $this->requestValidator->verifyAuthCallbackToken($klarnaQuote);
        } catch (LocalizedException $exception) {
            // If an exception is caught, the test will fail
            $this->fail('verifyAuthCallbackToken threw an exception: ' . $exception->getMessage());
        }

        $this->expectNotToPerformAssertions();
    }

    /**
     * @return void
     */
    public function testVerifyAuthCallbackTokenShouldThrowExceptionIfAuthCallbackTokenIsInvalid(): void
    {
        $this->dependencyMocks['request']
            ->method('getParam')
            ->willReturn('wrong-token');

        $klarnaQuote = $this->getMockForAbstractClass(QuoteAuthCallbackTokenInterface::class);
        $klarnaQuote
            ->method('getAuthTokenCallbackToken')
            ->willReturn('actual-token');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid value of "wrong-token" provided for the token field.');
        $this->requestValidator->verifyAuthCallbackToken($klarnaQuote);
    }

    public function testVerifyMagentoQuoteShouldNotThrowExceptionIfMagentoQuoteExistsAndIsActive(): void
    {
        $magentoQuote = $this->getMockForAbstractClass(CartInterface::class);
        $magentoQuote->method('getIsActive')
            ->willReturn(true);

        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willReturn($magentoQuote);

        try {
            $this->requestValidator->verifyMagentoQuote(10);
        } catch (LocalizedException $exception) {
            // If an exception is caught, the test will fail
            $this->fail('verifyMagentoQuote threw an exception: ' . $exception->getMessage());
        }

        $this->expectNotToPerformAssertions();
    }

    public function testVerifyMagentoQuoteShouldThrowExceptionIfMagentoQuoteIsNotExists(): void
    {
        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willThrowException(new NoSuchEntityException);

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity.');
        $this->requestValidator->verifyMagentoQuote(10);
    }

    public function testVerifyMagentoQuoteShouldThrowExceptionIfMagentoQuoteIsNotActive(): void
    {
        $magentoQuote = $this->getMockForAbstractClass(CartInterface::class);
        $magentoQuote->method('getIsActive')
            ->willReturn(false);

        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willReturn($magentoQuote);

        $quoteId = 10;

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("cartId = {$quoteId} is not active.");

        $this->requestValidator->verifyMagentoQuote($quoteId);
    }

    /**
     * @return array
     */
    public function requestBodyData()
    {
        return [
            [
                'data' => json_encode(''),
                'message' => 'session_id is required.',
            ],
            [
                json_encode([
                    'authorization_token' => 'a-random-value',
                ]),
                'message' => 'session_id is required.',
            ],
            [
                json_encode([
                    'session_id' => '',
                    'authorization_token' => 'a-random-value',
                ]),
                'message' => 'session_id is required.',
            ],
            [
                json_encode([
                    'session_id' => 'a-random-value',
                    'authorization_token' => '',
                ]),
                'message' => 'authorization_token is required.',
            ],
            [
                json_encode([
                    'session_id' => 'a-random-value',
                ]),
                'message' => 'authorization_token is required.',
            ],
        ];
    }

    protected function setUp(): void
    {
        $mockFactory   = new MockFactory($this);
        $this->objectFactory       = new TestObjectFactory($mockFactory);
        $this->requestValidator = $this->objectFactory->create(RequestValidator::class);
        $this->dependencyMocks = $this->objectFactory->getDependencyMocks();
    }
}
