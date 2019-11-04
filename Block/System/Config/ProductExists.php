<?php

namespace Diggecard\Giftcard\Block\System\Config;

use Diggecard\Giftcard\Helper\Status;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class ProductExists extends Field
{
    /** @var Status */
    private $status;

    /**
     * ProductExists constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Status $status,
        array $data = []
    )
    {
        $this->status = $status;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element)
    {
        $text = 'Product not created';
        $color = 'red';

        if ($this->status->isProductExists()) {
            $text = 'Product created';
            $color = 'green';
        }

        $html = '<td class="value">';
        $html .= "<span style='color:{$color}'>{$text}</span>";
        $html .= '</td>';

        return $html;
    }
}