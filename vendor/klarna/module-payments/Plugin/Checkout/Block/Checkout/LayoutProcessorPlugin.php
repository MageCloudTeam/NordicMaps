<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Plugin\Checkout\Block\Checkout;

use Klarna\Kp\Model\PaymentMethods\JsLayoutUpdater;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class LayoutProcessorPlugin
{
    /**
     * @var JsLayoutUpdater
     */
    private JsLayoutUpdater $jsLayoutUpdate;

    /**
     * @param JsLayoutUpdater $jsLayoutUpdater
     * @codeCoverageIgnore
     */
    public function __construct(JsLayoutUpdater $jsLayoutUpdater)
    {
        $this->jsLayoutUpdate = $jsLayoutUpdater;
    }

    /**
     * Checkout LayoutProcessor before process plugin.
     *
     * @param LayoutProcessor $processor
     * @param array           $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeProcess(LayoutProcessor $processor, array $jsLayout)
    {
        $configuration = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                          ['children']['payment']['children']['renders']['children'];

        if (empty($configuration['klarna'])) {
            return [$jsLayout];
        }

        $configuration = $this->jsLayoutUpdate->updateMethods($configuration);

        return [$jsLayout];
    }
}
