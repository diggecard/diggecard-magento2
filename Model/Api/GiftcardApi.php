<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Api;

use Diggecard\Giftcard\Api\Data\GiftcardApiInterface;
use Magento\Framework\HTTP\Client\Curl;
use Diggecard\Giftcard\Helper\Data as Json;
use Diggecard\Giftcard\Model\Config;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Zend_Http_Client;
use Zend_Http_Response;

/**
 * Class GiftcardApi
 *
 * @package Diggecard\Giftcard\Model\Api
 */
class GiftcardApi implements GiftcardApiInterface
{

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * GiftcardApi constructor.
     * @param CurlFactory $curlFactory
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        CurlFactory $curlFactory,
        Json $json,
        Config $config
    )
    {
        $this->json = $json;
        $this->config = $config;
        $this->curlFactory = $curlFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodUrl($method, $param = null)
    {
        if ($param) {
            return sprintf($this->config->getApiUrl()
                . $this->config->getApiContent()
                . $method);
        }
        return $this->config->getApiUrl()
            . $this->config->getApiContent()
            . $method;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareHeaders($headers = null)
    {
        $headers = $headers ?: ["Content-Type" => "application/json", "Authorization" => $this->config->getAuthorization()];
        return $headers;
    }

    /**
     * {@inheritDoc}
     */
    public function get($method, $data)
    {
        $httpAdapter = $this->curlFactory->create();
        $url = $this->getMethodUrl($method);
        $authorization = "Authorization: " . $this->config->getAuthorization();
        $httpAdapter->write(Zend_Http_Client::GET,
            $url . $data,
            '1.1',
            ["Content-Type:application/json", $authorization]);
        $result = $httpAdapter->read();
        $body = Zend_Http_Response::extractBody($result);
        $response = $this->json->unserialize($body);
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function post($method, $data)
    {
        $httpAdapter = $this->curlFactory->create();
        $url = $this->getMethodUrl($method);
        $authorization = "Authorization: " . $this->config->getAuthorization();
        $httpAdapter->write(Zend_Http_Client::POST,
            $url,
            '1.1',
            ["Content-Type:application/json", $authorization],
            \Zend\Json\Json::encode($data));
        $result = $httpAdapter->read();
        $body = Zend_Http_Response::extractBody($result);
        $response = $this->json->unserialize($body);
        return $response;
    }
}