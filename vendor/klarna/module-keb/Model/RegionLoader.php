<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Model;

use Magento\Directory\Model\RegionFactory;

/**
 * @internal
 */
class RegionLoader
{
    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;

    /**
     * @param RegionFactory $regionFactory
     * @codeCoverageIgnore
     */
    public function __construct(RegionFactory $regionFactory)
    {
        $this->regionFactory = $regionFactory;
    }

    /**
     * Sets region and region_id to address data by using the existing region code.
     *
     * @param array $addressData
     * @return array
     */
    public function addRegionToArray(array $addressData): array
    {
        if (!array_key_exists('region_code', $addressData) && !array_key_exists('country_id', $addressData)) {
            return $addressData;
        }
        if (empty($addressData['region_code'])) {
            return $addressData;
        }
        $regionFactory = $this->regionFactory->create();
        $regionFactory->loadByCode($addressData['region_code'], $addressData['country_id'])->getFirstItem();
        $addressData['region']    = $regionFactory->getName();
        $addressData['region_id'] = $regionFactory->getRegionId();

        return $addressData;
    }
}
