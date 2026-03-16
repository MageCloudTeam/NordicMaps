<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Model\Cart;

use Klarna\Kco\Model\Cart\FullUpdate;
use Klarna\Kco\Model\Checkout\Kco\Session;
use Klarna\Kco\Model\Cart\ShippingMethod\KlarnaRequestQuoteTransformer;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Kss\Model\Assignment\ShippingAddress;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Kss\Model\Checkout\Update;
use Klarna\Kss\Model\Factory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class FullUpdatePlugin
{
    /**
     * @var Session
     */
    private Session $kcoSession;
    /**
     * @var KlarnaRequestQuoteTransformer
     */
    private KlarnaRequestQuoteTransformer $klarnaRequestQuoteTransformer;
    /**
     * @var ShippingAddress
     */
    private ShippingAddress $shippingAddress;
    /**
     * @var Update
     */
    private Update $update;
    /**
     * @var Factory
     */
    private Factory $factory;

    /**
     * @param Session $kcoSession
     * @param KlarnaRequestQuoteTransformer $klarnaRequestQuoteTransformer
     * @param ShippingAddress $shippingAddress
     * @param Update $update
     * @param Factory $factory
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $kcoSession,
        KlarnaRequestQuoteTransformer $klarnaRequestQuoteTransformer,
        ShippingAddress $shippingAddress,
        Update $update,
        Factory $factory
    ) {
        $this->kcoSession = $kcoSession;
        $this->klarnaRequestQuoteTransformer = $klarnaRequestQuoteTransformer;
        $this->shippingAddress = $shippingAddress;
        $this->update = $update;
        $this->factory = $factory;
    }

    /**
     * Updating the KSS status before doing the full update
     *
     * @param FullUpdate $fullUpdate
     * @param DataObject $klarnaRequest
     * @param string $klarnaId
     * @param CartInterface $quote
     * @throws KlarnaException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeUpdateByKlarnaRequestObject(
        FullUpdate $fullUpdate,
        DataObject $klarnaRequest,
        string $klarnaId,
        CartInterface $quote
    ): void {
        if (count($klarnaRequest->getBillingAddress()) === 1 || count($klarnaRequest->getShippingAddress()) === 1) {
            return;
        }

        $this->kcoSession->setQuote($quote);

        if ($klarnaRequest->hasBillingAddress() &&
            $klarnaRequest->hasShippingAddress() &&
            $this->kcoSession->hasActiveKlarnaShippingGatewayInformation()) {

            $this->klarnaRequestQuoteTransformer->setQuoteShippingMethod($klarnaRequest, $klarnaId);
            $this->kcoSession->setKlarnaQuote(null);
            if ($this->kcoSession->hasActiveKlarnaShippingGatewayInformation()) {
                $this->shippingAddress->addKssAddressToShippingAddress($klarnaRequest);
            }
        }
    }

    /**
     * AUpdating the KSS status after doing the full update
     *
     * @param FullUpdate $fullUpdate
     * @param CartInterface $result
     * @param DataObject $klarnaRequest
     * @param string $klarnaId
     * @param CartInterface $quote
     * @return CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterUpdateByKlarnaRequestObject(
        FullUpdate $fullUpdate,
        CartInterface $result,
        DataObject $klarnaRequest,
        string $klarnaId,
        CartInterface $quote
    ): CartInterface {
        if (count($klarnaRequest->getBillingAddress()) === 1 || count($klarnaRequest->getShippingAddress()) === 1) {
            return $result;
        }

        if ($this->update->hasKssShippingStructure($klarnaRequest)) {
            $this->update->updateByCallbackRequest(
                $klarnaRequest,
                $this->factory->create($klarnaId),
                $quote->getStore()
            );
        } elseif ($this->kcoSession->hasActiveKlarnaShippingGatewayInformation()) {
            $this->update->disableKss($this->factory->create($klarnaId));
            $this->kcoSession->getKssValidator()->clearKssFlag();
        }

        return $result;
    }
}
