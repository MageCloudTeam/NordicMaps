<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Config;

use Klarna\Base\Api\VersionInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal
 */
class ApiVersion implements VersionInterface
{
    /**
     * @var string
     */
    private $type = 'payments';
    /**
     * @var string
     */
    private $code = 'kp_na';
    /**
     * @var bool
     */
    private $merchant_checkbox_support = false;
    /**
     * @var bool
     */
    private $date_of_birth_mandatory_support = false;
    /**
     * @var bool
     */
    private $phone_mandatory_support = false;
    /**
     * @var string
     */
    private $ordermanagement;
    /**
     * @var bool
     */
    private $title_mandatory_support = false;
    /**
     * @var bool
     */
    private $separate_tax_line = false;
    /**
     * @var bool
     */
    private $shipping_in_iframe = false;
    /**
     * @var bool
     */
    private $packstation_support = false;
    /**
     * @var string
     */
    private $production_url = 'https://api.klarna.com';
    /**
     * @var string
     */
    private $testdrive_url = 'https://api.playground.klarna.com';
    /**
     * @var bool
     */
    private $payment_review = false;
    /**
     * @var string
     */
    private $label = '';

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function isPackstationSupport()
    {
        return $this->packstation_support;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setting the type
     *
     * @param mixed $type
     * @return ApiVersion
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isMerchantCheckboxSupport()
    {
        return $this->merchant_checkbox_support;
    }

    /**
     * @inheritDoc
     */
    public function isDateOfBirthMandatorySupport()
    {
        return $this->date_of_birth_mandatory_support;
    }

    /**
     * @inheritDoc
     */
    public function isPhoneMandatorySupport()
    {
        return $this->phone_mandatory_support;
    }

    /**
     * @inheritDoc
     */
    public function getOrdermanagement()
    {
        return $this->ordermanagement;
    }

    /**
     * @inheritDoc
     */
    public function isTitleMandatorySupport()
    {
        return $this->title_mandatory_support;
    }

    /**
     * @inheritDoc
     */
    public function isSeparateTaxLine()
    {
        return $this->separate_tax_line;
    }

    /**
     * @inheritDoc
     */
    public function isShippingInIframe()
    {
        return $this->shipping_in_iframe;
    }

    /**
     * @inheritDoc
     */
    public function getUrl($testmode = true)
    {
        if ($testmode) {
            return $this->getTestdriveUrl();
        }
        return $this->getProductionUrl();
    }

    /**
     * Getting back the test drive url
     *
     * @return string
     */
    public function getTestdriveUrl()
    {
        return $this->testdrive_url;
    }

    /**
     * Getting back the production url
     *
     * @return string
     */
    public function getProductionUrl()
    {
        return $this->production_url;
    }

    /**
     * @inheritDoc
     */
    public function isPaymentReview()
    {
        return $this->payment_review;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Setting the code.
     *
     * @param string $code
     * @return ApiVersion
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
