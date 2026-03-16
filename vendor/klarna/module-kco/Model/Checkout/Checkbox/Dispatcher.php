<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Checkout\Checkbox;

use Klarna\Kco\Model\Configuration\Checkbox;
use Magento\Sales\Model\Order;
use Klarna\Base\Helper\KlarnaConfig;
use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Kco\Api\QuoteInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\DataObject;

/**
 * @internal
 */
class Dispatcher
{
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $eventManager;
    /**
     * @var Checkbox
     */
    private Checkbox $checkboxConfiguration;

    /**
     * @param KlarnaConfig $klarnaConfig
     * @param ManagerInterface $eventManager
     * @param Checkbox $checkboxConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaConfig $klarnaConfig,
        ManagerInterface $eventManager,
        Checkbox $checkboxConfiguration
    ) {
        $this->klarnaConfig = $klarnaConfig;
        $this->eventManager = $eventManager;
        $this->checkboxConfiguration = $checkboxConfiguration;
    }

    /**
     * Dispatch the merchant checkbox method
     *
     * @param array $args
     * @param StoreInterface $store
     * @return self
     * @throws LocalizedException
     */
    public function dispatchMerchantCheckboxMethod(array $args, StoreInterface $store): self
    {
        $merchantCheckboxMethod = $this->checkboxConfiguration->getOptions($store);
        if ($merchantCheckboxMethod === '-1') {
            return $this;
        }

        $methodConfig = $this->klarnaConfig->getMerchantCheckboxMethodConfig($merchantCheckboxMethod);
        $this->eventManager->dispatch('kco_' . $methodConfig->getSaveEvent(), $args);

        return $this;
    }

    /**
     * Dispatch events for multiple checkboxes
     *
     * @param DataObject $checkout
     * @param Order $order
     * @param CartInterface $magentoQuote
     * @param QuoteInterface $klarnaQuote
     * @throws LocalizedException
     */
    public function dispatchMultipleCheckboxesEvent(
        DataObject $checkout,
        Order $order,
        CartInterface $magentoQuote,
        QuoteInterface $klarnaQuote
    ): void {
        $checkboxesInfo = $checkout->getData('merchant_requested/additional_checkboxes');
        if (empty($checkboxesInfo)) {
            return;
        }

        foreach ($checkboxesInfo as $checkbox) {
            if (!isset($checkbox['id'])) {
                continue;
            }

            $this->eventManager->dispatch(
                'kco_' . $checkbox['id'] . '_save',
                [
                    'quote' => $magentoQuote,
                    'order' => $order,
                    'klarna_quote' => $klarnaQuote,
                    'checked' => (bool)$checkbox['checked']
                ]
            );
        }
    }
}
