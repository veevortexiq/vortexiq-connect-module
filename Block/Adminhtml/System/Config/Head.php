<?php
namespace Vortexiq\Connect\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Head extends Field
{
    /**
     * Rendertext
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = '';
        if ($element->getComment()) {
            $html .= '<div style="margin: auto; width: 40%;padding: 10px;">'
                     . $element->getComment()
                     . '</div>';
        }

        return $html;
    }

    /**
     * Return element html
     *
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
