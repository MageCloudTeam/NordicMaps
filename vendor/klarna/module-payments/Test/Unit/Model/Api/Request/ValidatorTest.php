<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Request;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Request\Orderline;
use Klarna\Kp\Model\Api\Request\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Request\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;

    public function testIsRequiredValueMissingEmptyAttributesAndEmptyDataImpliesNoError(): void
    {
        static::assertTrue($this->validator->isRequiredValueMissing([], ''));
    }

    public function testIsRequiredValueMissingEmptyAttributesAndNotEmptyDataImpliesNoError(): void
    {
        $this->validator->setData(['aa' => 'bb']);
        static::assertTrue($this->validator->isRequiredValueMissing([], ''));
    }

    public function testIsRequiredValueMissingNotEmptyAttributesAndEmptyDataImpliesError(): void
    {
        self::expectException(KlarnaApiException::class);
        $this->validator->isRequiredValueMissing(['aa'], '');
    }

    public function testIsRequiredValueMissingMissingFlatValueOfAttributeListImpliesError(): void
    {
        $this->validator->setData(['aa' => null]);

        self::expectException(KlarnaApiException::class);
        $this->validator->isRequiredValueMissing(['aa'], '');
    }

    public function testIsRequiredValueMissingEmptyArrayValueOfAttributeListImpliesError(): void
    {
        $this->validator->setData(['aa' => []]);

        self::expectException(KlarnaApiException::class);
        $this->validator->isRequiredValueMissing(['aa'], '');
    }

    public function testIsRequiredValueMissingFilledDataIsMatchingFilledAttributeListImpliesNoError(): void
    {
        $this->validator->setData(
            [
                'aa' => 'a',
                'bb' => ['cc']
            ]
        );
        self::assertTrue($this->validator->isRequiredValueMissing(['aa', 'bb'], ''));
    }

    public function testIsSumOrderLinesMatchingOrderLinesAndMissingOrderAmountEmptyDataImpliesError(): void
    {
        self::expectException(KlarnaApiException::class);
        $this->validator->isSumOrderLinesMatchingOrderAmount();
    }

    public function testIsSumOrderLinesMatchingOrderAmountMissingOrderLineDataImpliesError(): void
    {
        $this->validator->setData(['order_amount' => 3]);

        self::expectException(KlarnaApiException::class);
        $this->validator->isSumOrderLinesMatchingOrderAmount();
    }

    public function testIsSumOrderLinesMatchingOrderAmountMissingOrderAmountImpliesError(): void
    {
        $this->validator->setData(['order_lines' => []]);

        self::expectException(KlarnaApiException::class);
        $this->validator->isSumOrderLinesMatchingOrderAmount();
    }

    public function testIsSumOrderLinesMatchingOrderAmountNotEqualValuesImpliesError(): void
    {
        $this->validator->setData(
            [
                'order_lines' =>
                    [
                        ['total_amount' => 3],
                        ['total_amount' => 2]
                    ],
                'order_amount' => 10
            ]
        );

        self::expectException(KlarnaApiException::class);
        $this->validator->isSumOrderLinesMatchingOrderAmount();
    }

    public function testIsSumOrderLinesMatchingOrderAmountDifferentAlgebraicSignImpliesError(): void
    {
        $this->validator->setData(
            [
                'order_lines' =>
                    [
                        ['total_amount' => 3],
                        ['total_amount' => 2]
                    ],
                'order_amount' => -5
            ]
        );

        self::expectException(KlarnaApiException::class);
        $this->validator->isSumOrderLinesMatchingOrderAmount();
    }

    public function testIsSumOrderLinesMatchingOrderAmountSameValueImpliesNoError(): void
    {
        $this->validator->setData(
            [
                'order_lines' =>
                    [
                        ['total_amount' => 3],
                        ['total_amount' => 2]
                    ],
                'order_amount' => 5
            ]
        );

        static::assertTrue($this->validator->isSumOrderLinesMatchingOrderAmount());
    }

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->validator = $objectFactory->create(Validator::class);
    }
}
