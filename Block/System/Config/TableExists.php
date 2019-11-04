<?php

namespace Diggecard\Giftcard\Block\System\Config;

use Diggecard\Giftcard\Helper\Status;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class TableExists extends Field
{
    /** @var Status */
    private $status;

    /**
     * TableExists constructor.
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
        $text = __('Table not created');
        $color = 'red';

        if ($this->status->isTableExists()) {
            $text = __('Table created');
            $color = 'green';
        }

        $html = '<td class="value">';
        $html .= "<span style='color:{$color}'>{$text}</span>";
        $html .= '</td>';

        return $html;
    }
}