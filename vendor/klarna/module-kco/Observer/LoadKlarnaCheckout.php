<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Observer;

use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url;
use Magento\Framework\DataObjectFactory;

/**
 * This observer will be called when a customer reaches/opens the default Magento checkout page.
 * In this observer we decide if we forward the customer to the Klarna KCO page or if we do nothing.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class LoadKlarnaCheckout implements ObserverInterface
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var SettingsProvider
     */
    protected $config;
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param Manager           $manager
     * @param Url               $urlModel
     * @param Session           $session
     * @param SettingsProvider  $config
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        Manager $manager,
        Url $urlModel,
        Session $session,
        SettingsProvider $config,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->config            = $config;
        $this->url               = $urlModel;
        $this->manager           = $manager;
        $this->checkoutSession   = $session;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Loading the klarna checkout
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $overrideObject = $this->dataObjectFactory->create();
        $overrideObject->setData(
            [
                'force_disabled' => false,
                'force_enabled'  => false,
                'redirect_url'   => $this->url->getRouteUrl('checkout/klarna')
            ]
        );

        $this->manager->dispatch(
            'kco_override_load_checkout',
            [
                'override_object' => $overrideObject,
                'parent_observer' => $observer
            ]
        );

        if ($overrideObject->getForceEnabled()
            || (!$overrideObject->getForceDisabled()
                && !$this->checkoutSession
                    ->getKlarnaOverride()
                && $this->config->isKcoEnabled($this->checkoutSession->getQuote()->getStore()))
        ) {
            $observer->getControllerAction()->getResponse()
                ->setRedirect($overrideObject->getRedirectUrl())
                ->sendResponse();
        }
    }
}
