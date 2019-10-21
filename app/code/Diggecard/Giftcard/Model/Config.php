<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**#@+
     * Constants for config path.
     */
    const XML_PATH_API_KEY = 'diggecard/giftcard/api_key';
    const XML_PATH_API_URL = 'diggecard/giftcard/api_url';
    const XML_PATH_IFRAME_SRC = 'diggecard/giftcard/iframe_src';
    const XML_PATH_TIME_TO_RESERVE = 'diggecard/giftcard/time_to_reserve';
    const XML_PATH_HEADING = 'diggecard/giftcard/heading';
    /**#@-*/

    /**
     * Api Context
     */
    const API_CONTEXT = '/api/v1/partners';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve config value of Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieve config value of Authorization
     *
     * @return string
     */
    public function getAuthorization()
    {
        return 'DiggApiKey '.$this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieve config value of Api content
     *
     * @return string
     */
    public function getApiContent()
    {
        return self::API_CONTEXT;
    }

    /**
     * Retrieve config value of Api url
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieve config value of Iframe src
     *
     * @return string
     */
    public function getIframeSrc()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IFRAME_SRC,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieve config value of reserve time
     *
     * @return float
     */
    public function getTimeToReserve()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TIME_TO_RESERVE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Retrieve config value of reserve time
     *
     * @return float
     */
    public function getHeading()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADING,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
