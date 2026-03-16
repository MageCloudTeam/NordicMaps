<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package GDPR Base for Magento 2
 */

namespace Amasty\Gdpr\Api\Data;

interface WithConsentInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const CUSTOMER_ID = 'customer_id';
    public const DATE_CONSENTED = 'date_consented';
    public const POLICY_VERSION = 'policy_version';
    public const GOT_FROM = 'got_from';
    public const WEBSITE_ID = 'website_id';
    public const IP = 'ip';
    public const ACTION = 'action';
    public const CONSENT_CODE = 'consent_code';
    public const LOGGED_EMAIL = 'logged_email';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setId($id);

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getDateConsented();

    /**
     * @param string $dateConsented
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setDateConsented($dateConsented);

    /**
     * @return string
     */
    public function getPolicyVersion();

    /**
     * @param string $policyVersion
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setPolicyVersion($policyVersion);

    /**
     * @param string $from
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setGotFrom($from);

    /**
     * @return string
     */
    public function getGotFrom();

    /**
     * @param int $websiteId
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param string $ip
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setIp($ip);

    /**
     * @return string
     */
    public function getIp();

    /**
     * @param bool $action
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setAction($action);

    /**
     * @return bool
     */
    public function getAction();

    /**
     * @param int $consentCode
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setConsentCode($consentCode);

    /**
     * @return int
     */
    public function getConsentCode();

    /**
     * @param string $email
     *
     * @return \Amasty\Gdpr\Api\Data\WithConsentInterface
     */
    public function setLoggedEmail($email);

    /**
     * @return string
     */
    public function getLoggedEmail();
}
