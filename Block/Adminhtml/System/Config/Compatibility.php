<?php
namespace Vortexiq\Connect\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class Compatibility extends Field
{
    /**
     * Render text
     *
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    public function render(AbstractElement $element)
    {
        $html = '';
        if ($element->getComment()) {
            $html .= '<div id="vortexiq_compatibility" style="margin-left: 2em; width: 100%;padding: 10px; ">'
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
