<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Block\Giftcard;

use Diggecard\Giftcard\Model\GiftcardConfigProvider;
use Magento\Framework\View\Element\Template;
use Diggecard\Giftcard\Model\Config;

/**
 * Class Index
 *
 * @package Diggecard\Giftcard\Block\Giftcard
 */
class Index extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /** @var GiftcardConfigProvider */
    protected $_giftcardConfigProvider;

    /**
     * Index constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param GiftcardConfigProvider $giftcardConfigProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        GiftcardConfigProvider $giftcardConfigProvider,
        array $data = [])
    {
        $this->config = $config;
        $this->_giftcardConfigProvider = $giftcardConfigProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getIframeSrc()
    {
        return $this->config->getIframeSrc();
    }

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->config->getHeading();
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->_giftcardConfigProvider->isModuleEnable();
    }
}