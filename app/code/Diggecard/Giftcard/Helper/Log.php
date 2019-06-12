<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Log
 *
 * @package Diggecard\Giftcard\Helper
 */
class Log
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    const TYPE_INFO = 'info';
    const TYPE_ERROR = 'error';
    const TYPE_EXCEPTION = 'exception';

    /**
     * Log constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $message
     * @param $type
     *
     * @return bool
     */
    public function saveLog($message, $type = self::TYPE_INFO)
    {
        $path = 'diggecard/giftcard/enable_logging';
        $value = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($value) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Diggecard_giftcard.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->debug('=====================================================================================');
            switch ($type) {
                case "info":
                    $logger->info($message);
                    break;
                case "exception":
                    $logger->alert($message);
                    break;
                case "error":
                    $logger->err($message);
                    break;
                case "notice":
                    $logger->notice($message);
                    break;
            }
            return true;
        }
    }
}
