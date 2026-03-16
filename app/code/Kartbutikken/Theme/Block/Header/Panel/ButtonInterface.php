<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Block\Header\Panel;

use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Framework\View\Element\BlockInterface;

interface ButtonInterface extends SortLinkInterface, BlockInterface
{
    /**
     * Set button title
     *
     * @param $order
     *
     * @return $this
     */
    public function setSortOrder(int $order): self;

    /**
     * Return button title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set button title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self;

    /**
     * Return button additional classes
     *
     * @return array
     */
    public function getAdditionalClasses(): array;

    /**
     * Set button additional classes
     *
     * @param array $classes
     *
     * @return $this
     */
    public function setAdditionalClasses(array $classes): self;

    /**
     * Return is button link
     *
     * @return boolean
     */
    public function isLink(): bool;

    /**
     * Set is button link
     *
     * @param bool $isLink
     *
     * @return $this
     */
    public function setIsLink(bool $isLink): self;

    /**
     * Return button link url
     *
     * @return string
     */
    public function getLinkUrl(): string;

    /**
     * Set button link url
     *
     * @param string $linkUrl
     *
     * @return $this
     */
    public function setLinkUrl(string $linkUrl): self;

    /**
     * Return button label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Set button label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self;

    /**
     * Return button icon
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Set button icon
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(string $icon): self;

    /**
     * Return is dropdown
     *
     * @return bool
     */
    public function isDropDown(): bool;

    /**
     * Set is dropdown
     *
     * @param bool $isDropdown
     *
     * @return ButtonInterface
     */
    public function setIsDropDown(bool $isDropdown): self;

    /**
     * Return is custom content
     *
     * @return bool
     */
    public function isCustom(): bool;

    /**
     * Set is custom content
     *
     * @param bool $isCustom
     *
     * @return ButtonInterface
     */
    public function setIsCustom(bool $isCustom): self;

    /**
     * Return is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Set is disabled
     *
     * @param bool $isDisabled
     * @param bool $invert
     *
     * @return ButtonInterface
     */
    public function setIsDisabled(bool $isDisabled, bool $invert = false): self;
}