<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model\Checkout;

use Klarna\Kss\Api\ShippingMethodGatewayRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Kss\Api\ShippingMethodGatewayInterface;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Framework\DataObject;
use Klarna\Kss\Model\Assignment\ShippingMethodGateway;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository as MagentoQuoteRepository;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Performing updates in our KSS table and also on the Magento quote based on KSS values
 *
 * @internal
 */
class Update
{
    /**
     * @var ShippingMethodGatewayRepositoryInterface
     */
    private $repository;
    /**
     * @var ShippingMethodGateway
     */
    private $shippingAssignment;
    /**
     * @var MagentoQuoteRepository
     */
    private $quoteRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var KssConfigProvider
     */
    private $kssConfigProvider;

    /**
     * @param ShippingMethodGatewayRepositoryInterface $repository
     * @param ShippingMethodGateway                    $shippingAssignment
     * @param MagentoQuoteRepository                   $quoteRepository
     * @param LoggerInterface                          $logger
     * @param KssConfigProvider                        $kssConfigProvider
     * @codeCoverageIgnore
     */
    public function __construct(
        ShippingMethodGatewayRepositoryInterface $repository,
        ShippingMethodGateway $shippingAssignment,
        MagentoQuoteRepository $quoteRepository,
        LoggerInterface $logger,
        KssConfigProvider $kssConfigProvider
    ) {
        $this->repository         = $repository;
        $this->shippingAssignment = $shippingAssignment;
        $this->quoteRepository    = $quoteRepository;
        $this->logger             = $logger;
        $this->kssConfigProvider  = $kssConfigProvider;
    }

    /**
     * Creating or Updating the KSS information in the database based on the given Klarna api response object
     *
     * @param DataObject                     $response
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @return ShippingMethodGatewayInterface $shippingMethodGateway
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateStatusByApiResponse(
        DataObject $response,
        ShippingMethodGatewayInterface $shippingMethodGateway
    ): ShippingMethodGatewayInterface {
        if (!$this->hasKssShippingStructure($response)) {
            $this->disableKss($shippingMethodGateway);
            return $shippingMethodGateway;
        }

        if ($response->getIsSuccessful()) {
            $shippingMethodGateway = $this->shippingAssignment->assignByKlarnaInstance(
                $shippingMethodGateway,
                $response
            );
        }

        $this->repository->save($shippingMethodGateway);
        return $shippingMethodGateway;
    }

    /**
     * Creating or Updating the KSS information in the database based on the given Klarna callback request object
     *
     * @param DataObject                     $request
     * @param ShippingMethodGatewayInterface $shippingMethodGateway
     * @param StoreInterface                 $store
     * @return ShippingMethodGatewayInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateByCallbackRequest(
        DataObject $request,
        ShippingMethodGatewayInterface $shippingMethodGateway,
        StoreInterface $store
    ): ShippingMethodGatewayInterface {
        if (!$this->hasKssShippingStructure($request)) {
            $this->disableKss($shippingMethodGateway);
            return $shippingMethodGateway;
        }

        if (!$this->kssConfigProvider->isKssEnabled($store)) {
            $this->logger->forceLogging(
                'Warning: KSA is enabled on the API but disabled in the shop. This will lead to errors.'
            );

            return $shippingMethodGateway;
        }

        $shippingMethodGateway = $this->shippingAssignment->assignByKlarnaInstance(
            $shippingMethodGateway,
            $request
        );
        $this->repository->save($shippingMethodGateway);
        return $shippingMethodGateway;
    }

    /**
     * Disabling KSS by setting the respective flag.
     *
     * This scenario can happen when there will be a fallback to the native (not TMS like) shop shipping methods.
     *
     * @param ShippingMethodGatewayInterface $shipping
     * @return ShippingMethodGatewayInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function disableKss(ShippingMethodGatewayInterface $shipping): ShippingMethodGatewayInterface
    {
        if ($shipping->getId() === null) {
            return $shipping;
        }

        $shipping->setIsActive(false);
        $this->repository->save($shipping);

        return $shipping;
    }

    /**
     * Returns true if the the shipping node has not the KSS structure
     *
     * @param DataObject $request
     * @return bool
     */
    public function hasKssShippingStructure(DataObject $request): bool
    {
        $apiData = $request->getData();
        return isset($apiData['selected_shipping_option'], $apiData['selected_shipping_option']['shipping_method']);
    }
}
