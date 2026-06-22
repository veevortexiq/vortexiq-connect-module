<?php
namespace Vortexiq\Connect\Helper;

use Vortexiq\Connect\Helper\AbstractData;
use Psr\Log\LoggerInterface;

class Data extends AbstractData
{
    /**
     * @var logger
     */
    protected $logger;

    /**
     * @var storeManager
     */

    protected $storeManager;

    /**
     * Protected
     *
     * @var scopeConfig
     */
    protected $_scopeConfig;

    public const EXTENSION_NAME = 'Vortexiq_Connect';
    public const CONFIG_MODULE_PATH = 'vortexiq_connect';

    /**
     * Construct
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Function
     */
    public function getScopeconfig()
    {
        return $this->_scopeConfig;
    }
    /**
     * Function
     */
    public function isModuleEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue('vortexiq_connect_tab/connect_setting/connect_active', $storeScope);
    }
    /**
     * Function
     */
    public function currentDomain()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
    /**
     * Function
     */
    public function currentModuleName()
    {
        return self::EXTENSION_NAME;
    }
}
