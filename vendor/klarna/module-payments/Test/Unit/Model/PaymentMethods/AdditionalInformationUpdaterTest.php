<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\PaymentMethods\AdditionalInformationUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Magento\Quote\Model\Quote\Payment;
use Magento\Quote\Model\Quote as MagentoQuote;
use Magento\Framework\DataObject;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\AdditionalInformationUpdater
 */
class AdditionalInformationUpdaterTest extends TestCase
{
    /**
     * @var AdditionalInformationUpdater
     */
    private AdditionalInformationUpdater $additionalInformationUpdater;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Quote|MockObject
     */
    private KlarnaQuote $klarnaQuote;
    /**
     * @var Payment|MockObject
     */
    private Payment $payment;
    /**
     * @var DataObject|MockObject
     */
    private DataObject $additionalInformation;

    public function testUpdateByInputNoActiveQuoteWasFound(): void
    {
        $this->dependencyMocks['klarnaQuoteRepository']->expects(static::never())
            ->method('save');

        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willThrowException(new NoSuchEntityException(__('any message')));

        $this->additionalInformationUpdater->updateByInput($this->additionalInformation, $this->payment);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->additionalInformationUpdater = $objectFactory->create(AdditionalInformationUpdater::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->klarnaQuote = $mockFactory->create(KlarnaQuote::class);

        $magentoQuote = $mockFactory->create(MagentoQuote::class);
        $methodInstance = $mockFactory->create(Kp::class);
        $this->payment = $mockFactory->create(Payment::class);
        $this->payment->method('getQuote')
            ->willReturn($magentoQuote);
        $this->payment->method('getMethodInstance')
            ->willReturn($methodInstance);

        $this->additionalInformation = $mockFactory->create(DataObject::class);
    }
}
