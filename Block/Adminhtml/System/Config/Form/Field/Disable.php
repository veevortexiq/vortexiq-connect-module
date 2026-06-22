<?php

namespace Vortexiq\Connect\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

use Magento\Backend\Block\Template\Context;

class Disable extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var scopeConfig
     */

    public $scopeConfig;

     /**
      * Disable constructor.
      *
      * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
      * @param Context $context
      */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Context $context
    ) {

        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

 /**
  * GetEelment Function
  *
  * @param AbstractElement $element
  *
  * @return string
  */
    protected function _getElementHtml(AbstractElement $element)
    {
        $isActive = $this->scopeConfig->getValue(
            'vortexiq_connect_tab/module/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        if ($isActive!=1) {
            $element->setDisabled('disabled');
        }
        return $element->getElementHtml();
    }
}
