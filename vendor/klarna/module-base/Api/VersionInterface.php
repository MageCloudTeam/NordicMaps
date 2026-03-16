<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Api;

/**
 * Interface VersionInterface
 *
 * @api
 */
interface VersionInterface
{
    /**
     * Getting back the type
     *
     * @return string
     */
    public function getType();

    /**
     * Getting back the code
     *
     * @return string
     */
    public function getCode();

    /**
     * Getting back the label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Checking the merchant checkbox support flag
     *
     * @return bool
     */
    public function isMerchantCheckboxSupport();

    /**
     * Checking the date of birth mandatory support flag
     *
     * @return bool
     */
    public function isDateOfBirthMandatorySupport();

    /**
     * Checking the phone mandatory support flag.
     *
     * @return bool
     */
    public function isPhoneMandatorySupport();

    /**
     * Getting back the ordermanagement
     *
     * @return string
     */
    public function getOrdermanagement();

    /**
     * Checking the title mandatory support flag
     *
     * @return bool
     */
    public function isTitleMandatorySupport();

    /**
     * Checking the tax line flag
     *
     * @return bool
     */
    public function isSeparateTaxLine();

    /**
     * Checking the shipping in iframe flag
     *
     * @return bool
     */
    public function isShippingInIframe();

    /**
     * Getting back the url
     *
     * @param bool $testmode
     * @return string
     */
    public function getUrl($testmode = true);

    /**
     * Checking the payment review flag.
     *
     * @return bool
     */
    public function isPaymentReview();

    /**
     * Checking the packstation support flag.
     *
     * @return bool
     */
    public function isPackstationSupport();
}
