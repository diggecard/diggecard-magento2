<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Block\Giftcard;

use Diggecard\Giftcard\Helper\Data;
use Diggecard\Giftcard\Model\GiftcardConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
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

    /** @var Data */
    protected $_dataHelper;

    /** @var GiftcardConfigProvider */
    protected $_giftcardConfigProvider;

    /**
     * Index constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        Data $dataHelper,
        GiftcardConfigProvider $giftcardConfigProvider,
        array $data = [])
    {
        $this->config = $config;
        $this->_dataHelper = $dataHelper;
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

    public function isEnable()
    {
        return $this->_giftcardConfigProvider->isModuleEnable();
    }
}