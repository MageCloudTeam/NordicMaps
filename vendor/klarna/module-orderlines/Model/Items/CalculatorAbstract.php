<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items;

use Klarna\Base\Helper\DataConverter;

/**
 * @internal
 */
abstract class CalculatorAbstract
{

    /**
     * @var int
     */
    protected int $unitPrice;
    /**
     * @var int
     */
    protected int $taxRate;
    /**
     * @var int
     */
    protected int $totalAmount;
    /**
     * @var int
     */
    protected int $taxAmount;
    /**
     * @var string
     */
    protected string $title;
    /**
     * @var string
     */
    protected string $reference;
    /**
     * @var DataConverter
     */
    protected DataConverter $dataConverter;

    /**
     * @param DataConverter $dataConverter
     * @codeCoverageIgnore
     */
    public function __construct(DataConverter $dataConverter)
    {
        $this->dataConverter = $dataConverter;
        $this->reset();
    }

    /**
     * Setting the unit price
     *
     * @param int $unitPrice
     * @return self
     */
    public function setUnitPrice(int $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * Getting back the unit price
     *
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * Setting the tax rate
     *
     * @param int $taxRate
     * @return self
     */
    public function setTaxRate(int $taxRate): self
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * Getting back the tax rate
     *
     * @return int
     */
    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    /**
     * Setting the total amount
     *
     * @param int $totalAmount
     * @return self
     */
    public function setTotalAmount(int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * Getting back the total amount
     *
     * @return int
     */
    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    /**
     * Setting the tax amount
     *
     * @param int $taxAmount
     * @return self
     */
    public function setTaxAmount(int $taxAmount): self
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    /**
     * Getting back the tax amount
     *
     * @return int
     */
    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

    /**
     * Setting the title
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Getting back the title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Setting the reference
     *
     * @param string $reference
     * @return self
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Getting back the reference
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Resetting the values
     */
    protected function reset(): self
    {
        $this->setUnitPrice(0)
            ->setTaxRate(0)
            ->setTotalAmount(0)
            ->setTaxAmount(0)
            ->setTitle('')
            ->setReference('');

        return $this;
    }
}
