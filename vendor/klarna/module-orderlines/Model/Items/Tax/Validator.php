<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Items\Tax;

use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Orderlines\Model\Container\DataHolder;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class Validator
{
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;

    /**
     * @param KlarnaConfig $klarnaConfig
     * @codeCoverageIgnore
     */
    public function __construct(KlarnaConfig $klarnaConfig)
    {
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Returns true if its collectable
     *
     * @param DataHolder $dataHolder
     * @return bool
     */
    public function isCollectable(DataHolder $dataHolder): bool
    {
        return $this->klarnaConfig->isSeparateTaxLine($dataHolder->getStore());
    }

    /**
     * Returns true if its fetchable
     *
     * @param Parameter $parameter
     * @return bool
     */
    public function isFetchable(Parameter $parameter): bool
    {
        return $this->klarnaConfig->isSeparateTaxLine($parameter->getStore());
    }
}
