<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Kss\Api\ShippingMethodGatewayRepositoryInterface;

/**
 * Performing different high level KSS validations
 *
 * @internal
 */
class Validator
{
    public const CHECK_NOT_SET = -1;
    public const CHECK_RESULT_USED = 1;
    public const CHECK_RESULT_NOT_USED = 0;

    /**
     * @var ShippingMethodGatewayRepositoryInterface
     */
    private $shippingMethodGatewayRepository;
    /**
     * @var KssConfigProvider
     */
    private $configProvider;
    /**
     * @var int
     */
    private $isKssUsed = self::CHECK_NOT_SET;

    /**
     * @param ShippingMethodGatewayRepositoryInterface $shippingMethodGatewayRepository
     * @param KssConfigProvider                        $configProvider
     * @codeCoverageIgnore
     */
    public function __construct(
        ShippingMethodGatewayRepositoryInterface $shippingMethodGatewayRepository,
        KssConfigProvider $configProvider
    ) {
        $this->shippingMethodGatewayRepository = $shippingMethodGatewayRepository;
        $this->configProvider                  = $configProvider;
    }

    /**
     * Checking the state of the KSS entry for the given klarna checkout id and returns true if its used for it.
     *
     * @param StoreInterface $store
     * @param string         $klarnaCheckoutId
     * @return bool
     */
    public function isKssUsed(StoreInterface $store, string $klarnaCheckoutId): bool
    {
        if (!$this->configProvider->isKssEnabled($store)) {
            $this->setKssUsed(self::CHECK_RESULT_NOT_USED);
            return false;
        }

        try {
            $shipping = $this->shippingMethodGatewayRepository->loadShipping($klarnaCheckoutId, 'klarna_session_id');
        } catch (NoSuchEntityException $e) {
            $this->setKssUsed(self::CHECK_RESULT_NOT_USED);
            return false;
        }

        if (!$shipping->isActive()) {
            $this->setKssUsed(self::CHECK_RESULT_NOT_USED);
            return false;
        }

        $this->setKssUsed(self::CHECK_RESULT_USED);
        return true;
    }

    /**
     * Setting the KSS flag into the attribute
     *
     * @param int $value
     * @return int
     */
    public function setKssUsed(int $value): int
    {
        $this->isKssUsed = $value;
        return $this->isKssUsed;
    }

    /**
     * Clearing the value of the KSS flag
     */
    public function clearKssFlag(): void
    {
        $this->isKssUsed = self::CHECK_NOT_SET;
    }

    /**
     * Getting back the flag which indicates if KSS is used or not
     *
     * @return int
     */
    public function getKssUsedFlag(): int
    {
        return $this->isKssUsed;
    }
}
