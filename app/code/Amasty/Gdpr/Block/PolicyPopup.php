<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package GDPR Base for Magento 2
 */

namespace Amasty\Gdpr\Block;

use Amasty\Gdpr\Model\Config;
use Magento\Framework\View\Element\Template;

class PolicyPopup extends Template
{
    /**
     * @var string
     */
    protected $_template = 'policy_popup.phtml';

    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        Config $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function getTextUrl()
    {
        return $this->getUrl('gdpr/policy/policytext');
    }

    /**
     * @return string
     */
    public function getPopupDataUrl()
    {
        return $this->getUrl('gdpr/policy/popupData');
    }

    /**
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->getUrl('gdpr/policy/accept');
    }

    /**
     * @return bool
     */
    public function showOnPageLoad()
    {
        return ($this->configProvider->isModuleEnabled()
            && $this->configProvider->isDisplayPpPopup());
    }

    /**
     * @return string
     */
    public function getPolicyNotificationText()
    {
        return __("We would like to inform you that our Privacy Policy has been amended.")->render().
        __("Please, read and accept the new terms.")->render();
    }
}
