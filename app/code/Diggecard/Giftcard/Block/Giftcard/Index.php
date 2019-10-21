<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Block\Giftcard;

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

    /**
     * Index constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = [])
    {
        $this->config = $config;
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
}