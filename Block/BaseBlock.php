<?php

namespace Vortexiq\Connect\Block;

use Magento\Framework\UrlFactory;

class BaseBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vortexiq\Connect\Helper\Data
     */
     protected $_devToolHelper;

     /**
      * @var \Magento\Framework\Url
      */
     protected $_urlApp;

     /**
      * @var \Vortexiq\Connect\Model\Config
      */
    protected $_config;

    /**
     * @param \Vortexiq\Connect\Block\Context $context
     */
    public function __construct(\Vortexiq\Connect\Block\Context $context)
    {
        $this->_devToolHelper = $context->getConnectHelper();
        $this->_config = $context->getConfig();
        $this->_urlApp=$context->getUrlFactory()->create();
        parent::__construct($context);
    }

    /**
     * Function for getting event details
     *
     * @return array
     */
    public function getEventDetails()
    {
        return  $this->_devToolHelper->getEventDetails();
    }

    /**
     * Function for getting current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlApp->getCurrentUrl();
    }

    /**
     * Function for getting controller url for given router path
     *
     * @param string $routePath
     * @return string
     */
    public function getControllerUrl($routePath)
    {

        return $this->_urlApp->getUrl($routePath);
    }

    /**
     * Function for getting current url
     *
     * @param string $path
     * @return string
     */
    public function getConfigValue($path)
    {
        return $this->_config->getCurrentStoreConfigValue($path);
    }

    /**
     * Function canShowConnect
     *
     * @return bool
     */
    public function canShowConnect()
    {
        $isEnabled=$this->getConfigValue('vortexiq_connect/module/is_enabled');
        if ($isEnabled) {
            $allowedIps=$this->getConfigValue('vortexiq_connect/module/allowed_ip');
            if (is_null($allowedIps)) { // phpcs:ignore
                return true;
            } else {
                $remoteIp=$_SERVER['REMOTE_ADDR']; //phpcs:ignore
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}
