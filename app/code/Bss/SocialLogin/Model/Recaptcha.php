<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SocialLogin\Model;

use Laminas\Http\Client;
use Laminas\Http\Request as HttpRequest;
use Laminas\OAuth\Consumer;

class Recaptcha
{
    public const REQUEST_URL = 'https://www.google.com/recaptcha/api/siteverify';
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteip;

    /**
     * @var null
     */
    protected $client;

    /**
     * Recaptcha constructor.
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteip
     */
    public function __construct(
        \Bss\SocialLogin\Helper\Data $helper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteip
    ) {
        $this->helper = $helper;
        $this->remoteip = $remoteip;
    }

    /**
     * Verify
     *
     * @param string $recaptcha_response
     * @return mixed|string
     */
    public function verify($recaptcha_response)
    {
        $params = [
            'secret'   => $this->helper->getSecretKey(),
            'response' => $recaptcha_response,
            'remoteip' => $this->remoteip->getRemoteAddress()
        ];

        $client = $this->getHttpClient();
        $client->setParameterPost($params);
        $errors = '';

        try {
            $response = $client->send();
            $data = json_decode($response->getBody());
            if (array_key_exists('error-codes', $data)) {
                $errors = $data['error-codes'];
            }
        } catch (\Exception $e) {
            $data = ['success' => false];
        }

        return $errors;
    }

    /**
     * Set http client
     *
     * @param mixed $client
     * @return $this
     */
    public function setHttpClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get Http Client
     *
     * @return Client|null
     */
    public function getHttpClient()
    {
        if ($this->client == null) {
            $this->client = new Client();
        }

        $this->client->setUri(self::REQUEST_URL);

        return $this->client;
    }
}
