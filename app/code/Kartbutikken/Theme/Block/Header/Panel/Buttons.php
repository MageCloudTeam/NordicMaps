<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Block\Header\Panel;

use Hryvinskyi\Base\Helper\ArrayHelper;
use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Buttons
 */
class Buttons extends Template implements IdentityInterface
{
    /**
     * @var ButtonInterface[]
     */
    private $buttons;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'header/panel/buttons.phtml';

    /**
     * @var array
     */
    private $cacheInfo = [];

    /**
     * @var HttpContext
     */
    private $httpContext;

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
     * Get links
     *
     * @return ButtonInterface[]
     */
    public function getButtons(): array
    {
        if ($this->buttons === null) {
            /** @var ButtonInterface[] $buttons */
            $buttons = $this->_layout->getChildBlocks($this->getNameInLayout());
            $sortableButtons = [];

            foreach ($buttons as $key => $button) {
                if ($button->isDisabled()) {
                    unset($buttons[$key]);
                    continue;
                }
                if ($button instanceof SortLinkInterface) {
                    $sortableButtons[] = $button;
                    unset($buttons[$key]);
                }
            }

            usort($sortableButtons, [$this, "compare"]);

            $this->buttons = array_merge($sortableButtons, $buttons);
        }

        return $this->buttons;
    }

    /**
     * Compare sortOrder in buttons.
     *
     * @param SortLinkInterface $firstButton
     * @param SortLinkInterface $secondButton
     *
     * @return int
     */
    private function compare(SortLinkInterface $firstButton, SortLinkInterface $secondButton): int
    {
        return $firstButton->getSortOrder() <=> $secondButton->getSortOrder();
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        foreach ($this->_layout->getChildBlocks($this->getNameInLayout()) as $key => $button) {
            $this->cacheInfo[$key] = ['disabled ' => $button->isDisabled(), 'label' => $button->getLabel(), 'title' => $button->getTitle()];
        }

        return ArrayHelper::merge(parent::getCacheKeyInfo(), ['buttons' => serialize($this->cacheInfo), 'identities' => $this->getIdentities()]);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return ['button_logged_in' => $this->httpContext->getValue(Context::CONTEXT_AUTH)];
    }
}
