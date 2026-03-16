<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Configuration;

use Klarna\Base\Model\Configuration\AbstractConfiguration;
use Klarna\Kco\Model\Payment\Kco;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class MandatoryFields extends AbstractConfiguration
{
    /**
     * @var string
     */
    protected string $paymentCode = Kco::METHOD_CODE;

    /**
     * Returns true if the national identification number field is mandatory
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isNationalIdentificationNumberMandatory(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'national_identification_number_mandatory');
    }

    /**
     * Returns true if the date of birth field is mandatory
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isDateOfBirthMandatory(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'dob_mandatory');
    }

    /**
     * Returns true if the title field is mandatory
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isTitleMandatory(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'title_mandatory');
    }

    /**
     * Returns true if the phone field is mandatory
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isPhoneMandatory(StoreInterface $store): bool
    {
        return $this->getCheckoutFlagValue($store, 'phone_mandatory');
    }
}
