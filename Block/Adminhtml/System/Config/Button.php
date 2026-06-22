<?php

namespace Vortexiq\Connect\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Vortexiq\Connect\Helper\AbstractData;
use Vortexiq\Connect\Helper\Validate;

class Button extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Vortexiq_Connect::system/config/button.phtml';

    /**
     * @var AbstractData
     */
    protected $_helper;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param Validate $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Validate $helper,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * GET
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $activeButton = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData([
                'id'      => 'vortexiq_module_active',
                'label'   => __('Activate Now'),
                'onclick' => 'javascript:vortexiqModuleActive(); return false;',
            ]);

        $cancelButton = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData([
                'id'      => 'vortexiq_module_update',
                'label'   => __('Update this license'),
                'onclick' => 'javascript:vortexiqModuleUpdate(); return false;',
            ]);

        return $activeButton->toHtml() . $cancelButton->toHtml();
    }

    /**
     * Render button
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * GET
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return '';
    }

    /**
     * Return element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();

        $path = explode('/', $originalData['path']);
        $this->addData(
            [
                'xc_is_active'       => $this->_helper->isModuleActive($originalData['module_name']),
                'xc_module_name'     => $originalData['module_name'],
                'xc_module_type'     => $originalData['module_type'],
                'xc_active_url'      => $this->getUrl('vortexiqconnect/index/activate'),
                'xc_module_html_id'  => implode('_', $path)
            ]
        );

        return $this->_toHtml();
    }
}
