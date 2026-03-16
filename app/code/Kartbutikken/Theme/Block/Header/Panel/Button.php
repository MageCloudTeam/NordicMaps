<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Block\Header\Panel;

use Hryvinskyi\Base\Helper\ArrayHelper;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Button
 */
class Button extends Template implements ButtonInterface, IdentityInterface
{
    /**
     * @var int
     */
    private $sortOrder = 0;

    /**
     * @var boolean
     */
    private $isDropDown = false;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var boolean
     */
    private $isLink = false;

    /**
     * @var string
     */
    private $linkUrl = '#';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $icon = '';

    /**
     * @var boolean
     */
    private $isCustom = false;

    /**
     * @var boolean
     */
    private $isDisabled = false;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'header/panel/button.phtml';

    /**
     * @var array
     */
    private $additionalClasses = [];

    /**
     * Customer session
     *
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Button constructor.
     *
     * @param Template\Context $context
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HttpContext $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->httpContext = $httpContext;
    }

    /**
     * Get sort order for block.
     *
     * @return int
     * @since 101.0.0
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set button title
     *
     * @param $order
     *
     * @return $this
     */
    public function setSortOrder(int $order): ButtonInterface
    {
        $this->sortOrder = $order;

        return $this;
    }

    /**
     * Return button title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set button title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): ButtonInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return button additional classes
     *
     * @return array
     */
    public function getAdditionalClasses(): array
    {
        return $this->additionalClasses;
    }

    /**
     * Set button additional classes
     *
     * @param array $classes
     *
     * @return $this
     */
    public function setAdditionalClasses(array $classes): ButtonInterface
    {
        $this->additionalClasses = $classes;

        return $this;
    }

    /**
     * Return is button link
     *
     * @return boolean
     */
    public function isLink(): bool
    {
        return $this->isLink;
    }

    /**
     * Set is button link
     *
     * @param bool $isLink
     *
     * @return $this
     */
    public function setIsLink(bool $isLink): ButtonInterface
    {
        $this->isLink = $isLink;

        return $this;
    }

    /**
     * Return button link url
     *
     * @return string
     */
    public function getLinkUrl(): string
    {
        return $this->linkUrl;
    }

    /**
     * Set button link url
     *
     * @param string $linkUrl
     *
     * @return $this
     */
    public function setLinkUrl(string $linkUrl): ButtonInterface
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    /**
     * Return button label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set button label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): ButtonInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Return button icon
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Set button icon
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(string $icon): ButtonInterface
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return is dropdown
     *
     * @return bool
     */
    public function isDropDown(): bool
    {
        return $this->isDropDown;
    }

    /**
     * Set is dropdown
     *
     * @param bool $isDropdown
     *
     * @return ButtonInterface
     */
    public function setIsDropDown(bool $isDropdown): ButtonInterface
    {
        $this->isDropDown = $isDropdown;

        return $this;
    }

    /**
     * Return is custom content
     *
     * @return bool
     */
    public function isCustom(): bool
    {
        return $this->isCustom;
    }

    /**
     * Set is custom content
     *
     * @param bool $isCustom
     *
     * @return ButtonInterface
     */
    public function setIsCustom(bool $isCustom): ButtonInterface
    {
        $this->isCustom = $isCustom;

        return $this;
    }

    /**
     * Return is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    /**
     * Set is disabled
     *
     * @param bool $isDisabled
     * @param bool $invert
     *
     * @return ButtonInterface
     */
    public function setIsDisabled(bool $isDisabled, bool $invert = false): ButtonInterface
    {
        if ($invert) {
            $isDisabled = !$isDisabled;
        }

        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return ArrayHelper::merge(parent::getCacheKeyInfo(), [
            'is_logged_in' => $this->httpContext->getValue(Context::CONTEXT_AUTH),
            'is_disabled' => $this->isDisabled()
        ]);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return $this->getCacheKeyInfo();
    }
}
