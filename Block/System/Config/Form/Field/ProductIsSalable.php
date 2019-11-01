<?php

namespace Diggecard\Giftcard\Block\System\Config\Form\Field;

use Diggecard\Giftcard\Helper\Status;
use Magento\Framework\Option\ArrayInterface;

class ProductIsSalable implements ArrayInterface
{
    /** @var Status $status */
    protected $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->status->isProductSalable())
            return [['value' => 1, 'label' => __('Yes')]];

        return [['value' => 0, 'label' => __('No')]];
    }
}
