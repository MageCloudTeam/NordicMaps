<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Response;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Response
 */
class ResponseTest extends TestCase
{
    /**
     * @var Response
     */
    private Response $response;
    /**
     * @var TestObjectFactory
     */
    private TestObjectFactory $objectFactory;

    public function testGetSessionIdReturnsValue(): void
    {
        static::assertEquals('my_session_id', $this->response->getSessionId());
    }

    public function testGetClientTokenReturnsValue(): void
    {
        static::assertEquals('my_client_token', $this->response->getClientToken());
    }

    public function testGetResponseCodeReturnsValue(): void
    {
        static::assertEquals(200, $this->response->getResponseCode());
    }

    public function testGetOrderIdReturnsValue(): void
    {
        static::assertEquals('my_order_id', $this->response->getOrderId());
    }

    public function testGetRedirectUrlReturnsValue(): void
    {
        static::assertEquals('my_redirect_url', $this->response->getRedirectUrl());
    }

    public function testGetPaymentMethodCategoriesReturnsValue(): void
    {
        static::assertSame(['payment_method_1', 'payment_method_2'], $this->response->getPaymentMethodCategories());
    }

    public function testGetFraudStatusReturnsValue(): void
    {
        static::assertSame('my_fraud_status', $this->response->getFraudStatus());
    }

    public function testIsSuccessfullStatusCodeIs200(): void
    {
        static::assertTrue($this->response->isSuccessfull());
    }

    public function testIsSuccessfullStatusCodeIs201(): void
    {
        $response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => ['response_code' => 201]
            ]
        );

        static::assertTrue($response->isSuccessfull());
    }

    public function testIsSuccessfullStatusCodeIs204(): void
    {
        $response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => ['response_code' => 204]
            ]
        );

        static::assertTrue($response->isSuccessfull());
    }

    public function testIsSuccessfulInvalidStatusCode(): void
    {
        $response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => ['response_code' => 400]
            ]
        );

        static::assertFalse($response->isSuccessfull());
    }

    public function testGetMessageReturnsValue(): void
    {
        static::assertEquals('my_message', $this->response->getMessage());
    }

    public function testGetResponseStatusMessageReturnsValue(): void
    {
        static::assertEquals('my_response_status_message', $this->response->getResponseStatusMessage());
    }

    public function testIsExpiredSessionIsExpired(): void
    {
        $response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => ['response_code' => Response::RESPONSE_STATUS_NOT_FOUND]
            ]
        );

        static::assertTrue($response->isExpired());
    }

    public function testIsExpiredSessionIsNotExpired(): void
    {
        $response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => ['response_code' => 200]
            ]
        );

        static::assertFalse($response->isExpired());
    }

    public function testGetAttachmentReturnsValue(): void
    {
        static::assertSame(['content_type' => 'my_content_type', 'body' => 'my_body'], $this->response->getAttachment());
    }

    public function testGetCustomerReturnsValue(): void
    {
        static::assertSame(['name' => 'my_customer_name'], $this->response->getCustomer());
    }

    public function testGetBillingAddressReturnsValue(): void
    {
        static::assertSame(['firstname' => 'my_firstname'], $this->response->getBillingAddress());
    }

    public function testGetShippingAddressReturnsValue(): void
    {
        static::assertSame(['lastname' => 'my_lastname'], $this->response->getShippingAddress());
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $this->objectFactory = new TestObjectFactory($mockFactory);

        $input = [
            'session_id' => 'my_session_id',
            'client_token' => 'my_client_token',
            'response_code' => 200,
            'order_id' => 'my_order_id',
            'redirect_url' => 'my_redirect_url',
            'payment_method_categories' => ['payment_method_1', 'payment_method_2'],
            'fraud_status' => 'my_fraud_status',
            'message' => 'my_message',
            'response_status_message' => 'my_response_status_message',
            'attachment' => [
                'content_type' => 'my_content_type',
                'body' => 'my_body'
            ],
            'customer' => [
                'name' => 'my_customer_name'
            ],
            'billing_address' => [
                'firstname' => 'my_firstname'
            ],
            'shipping_address' => [
                'lastname' => 'my_lastname'
            ]
        ];
        $this->response = $this->objectFactory->create(
            Response::class,
            [],
            [
                'data' => $input
            ]
        );
    }
}
