<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Api;

use Diggecard\Giftcard\Api\GiftcardApiRepositoryInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Diggecard\Giftcard\Helper\Data as Json;
use Diggecard\Giftcard\Model\Config;

/**
 * Class GiftcardApiRepository
 *
 * @package Diggecard\Giftcard\Model\Api
 */
class GiftcardApiRepository extends GiftcardApi implements GiftcardApiRepositoryInterface
{
    /**
     * GiftcardApiRepository constructor.
     * @param CurlFactory $curlFactory
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        CurlFactory $curlFactory,
        Json $json,
        Config $config
    ) {
        parent::__construct(
            $curlFactory,
            $json,
            $config
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getGiftCardByQrCode($qrCode)
    {
        return $this->get(GiftcardApiRepositoryInterface::GET_GIFTCARD_QR_CODE, $qrCode);
    }

    /**
     * {@inheritDoc}
     */
    public function postGiftcardIssue($data)
    {
        return $this->get(GiftcardApiRepositoryInterface::POST_GIFTCARD_ISSUE, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function postReserveGiftcardAmount($data)
    {
        return $this->post(GiftcardApiRepositoryInterface::POST_GIFTCARD_RESERVE, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function postCaptureReservedGiftcardAmount($data)
    {
        return $this->post(GiftcardApiRepositoryInterface::POST_GIFTCARD_CAPTURE, $data);
    }
}