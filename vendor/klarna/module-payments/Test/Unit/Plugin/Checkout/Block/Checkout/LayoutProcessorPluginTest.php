<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Plugin\Checkout\Block\Checkout;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Plugin\Checkout\Block\Checkout\LayoutProcessorPlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Klarna\Base\Exception as KlarnaException;

class LayoutProcessorPluginTest extends TestCase
{
    /**
     * @var LayoutProcessorPlugin
     */
    private LayoutProcessorPlugin $model;
    /**
     * @var LayoutProcessor|MockObject
     */
    private LayoutProcessor $layoutProcessor;
    /**
     * @var array|MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var KlarnaQuote|MockObject
     */
    private KlarnaQuote $klarnaQuote;

    public function testBeforeProcessGeneralConfigurationDoesNotExist(): void
    {
        $expected = [
            [
                'components' => [
                    'checkout' => [
                        'children' => [
                            'steps' => [
                                'children' => [
                                    'billing-step' => [
                                        'children' => [
                                            'payment' => [
                                                'children' => [
                                                    'renders' => [
                                                        'children' => null
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $result = $this->model->beforeProcess($this->layoutProcessor, []);
        static::assertSame($expected, $result);
    }

    public function testBeforeProcessKlarnaConfigurationDoesNotExist(): void
    {
        $input = [
                'components' => [
                    'checkout' => [
                        'children' => [
                            'steps' => [
                                'children' => [
                                    'billing-step' => [
                                        'children' => [
                                            'payment' => [
                                                'children' => [
                                                    'renders' => [
                                                        'children' => [

                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
        ];

        $result = $this->model->beforeProcess($this->layoutProcessor, $input);
        static::assertSame([$input], $result);
    }

    public function testBeforeProcessKpIsDisabled(): void
    {
        $input = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'renders' => [
                                                    'children' => [
                                                        'klarna' => [

                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->dependencyMocks['jsLayoutUpdater']->method('updateMethods')
            ->willReturn($input);
        $result = $this->model->beforeProcess($this->layoutProcessor, $input);
        static::assertSame([$input], $result);
    }

    public function testBeforeProcessNoKpMethodsExistsInDatabase(): void
    {
        $input = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'renders' => [
                                                    'children' => [
                                                        'klarna' => [

                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->dependencyMocks['jsLayoutUpdater']->method('updateMethods')
            ->willReturn($input);
        $this->klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([]);

        $result = $this->model->beforeProcess($this->layoutProcessor, $input);
        static::assertSame([$input], $result);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(LayoutProcessorPlugin::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->layoutProcessor = $mockFactory->create(LayoutProcessor::class);
        $this->klarnaQuote = $mockFactory->create(KlarnaQuote::class);
    }
}
