<?php
namespace Vortexiq\Connect\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\PackageInfoFactory;

class Version extends Field
{
    /**
     * @var PackageInfoFactory
     */
    protected $_packageInfoFactory;

    /**
     * Version constructor.
     *
     * @param Context $context
     * @param PackageInfoFactory $packageInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PackageInfoFactory $packageInfoFactory,
        array $data = []
    ) {
        $this->_packageInfoFactory = $packageInfoFactory;

        parent::__construct($context, $data);
    }

    /**
     *  Function
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();

        $packageInfo = $this->_packageInfoFactory->create();
        $version = $packageInfo->getVersion($originalData['module_name']);

        return '<div class="control-value special">' . $version . '</div>';
    }

    /**
     * Function
     *
     * @param AbstractElement $element
     * @param string $html
     *
     * @return string
     */
    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" class="row_vortexiq_module_version">' . $html . '</tr>';
    }
}
