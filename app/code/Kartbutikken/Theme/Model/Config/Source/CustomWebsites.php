<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\WebsiteRepository;

/**
 * Class CustomWebsites
 */
class CustomWebsites implements OptionSourceInterface
{
    /**
     * @var WebsiteRepository
     */
    private $websiteRepository;

    /**
     * CustomWebsites constructor.
     *
     * @param WebsiteRepository $websiteRepository
     */
    public function __construct(
        WebsiteRepository $websiteRepository
    ) {
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $websites = $this->websiteRepository->getList();
        $return = [];
        foreach ($websites as $website) {
            if ($website->getCode() === 'admin') {
                continue;
            }

            $return[] = [
                'value' => $website->getCode(),
                'label' => $website->getName(),
            ];
        }

        return $return;
    }
}