<?php
namespace Vortexiq\Connect\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Vortexiq\Connect\Model\ActivateFactory;

class Validate extends AbstractData
{
    public const MODULE_TYPE_FREE = 1;
    public const MODULE_TYPE_PAID = 2;
    public const DEV_ENV = [];

    /**
     * @var array
     */
    protected $configModulePath = [];

    /**
     * @var array
     */
    protected $_vortexiqModules; // phpcs:ignore

    /**
     * @var ModuleListInterface
     */
    protected $_moduleList; // phpcs:ignore
      /**
       * @var activateFactory
       */
    protected $activateFactory;

    /**
     * Validate constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ModuleListInterface $moduleList
     * @param ActivateFactory $activateFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ModuleListInterface $moduleList,
        ActivateFactory $activateFactory
    ) {
        $this->_moduleList = $moduleList;
        $this->activateFactory = $activateFactory;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * NEEDActive
     *
     * @param mixed $moduleName
     *
     * @return bool
     */
    public function needActive($moduleName)
    {
        $type = $this->getModuleType($moduleName);

        return $type && $type === self::MODULE_TYPE_FREE;
    }

    /**
     * GetModuleType
     *
     * @param mixed $moduleName
     *
     * @return mixed
     */
    public function getModuleType($moduleName)
    {
        return (int) $this->getModuleData($moduleName, 'type') ?: self::MODULE_TYPE_PAID;
    }

    /**
     * GetModule
     *
     * @param string $moduleName
     * @param string $field
     *
     * @return array|mixed
     */
    public function getModuleData($moduleName, $field = '')
    {
        $configModulePath = $this->getConfigModulePath($moduleName);
        return $this->getConfigValue($configModulePath . '_tab/module/' . $field);
    }

    /**
     * GetConfig
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public function getConfigModulePath($moduleName)
    {

        if (!isset($this->configModulePath[$moduleName])) {
            $this->configModulePath[$moduleName] = false;
            $helperClassName = str_replace('_', '\\', $moduleName) . '\Helper\Data';
            if (class_exists($helperClassName)) {
                $helper = $this->objectManager->get($helperClassName);
                if ($helper instanceof AbstractData) {
                    $this->configModulePath[$moduleName] = $helper::CONFIG_MODULE_PATH;
                }
            }
        }

        return $this->configModulePath[$moduleName];
    }

    /**
     * IsModule
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public function isModuleActive($moduleName)
    {
        $configModulePath = $this->getConfigModulePath($moduleName);

        return $this->
        getConfigValue($configModulePath . '_tab/module/product_key') && $this->checkExtensionStatus($moduleName);
    }

    /**
     * GetModuleActive
     *
     * @param string $moduleName
     *
     * @return string
     */
    public function getModuleCheckbox($moduleName)
    {
        $create = $this->getModuleData($moduleName, 'create');
        if ($create === null) {
            $create = 1;
        }

        $subscribe = $this->getModuleData($moduleName, 'subscribe');
        if ($subscribe === null) {
            $subscribe = 1;
        }

        return self::jsonEncode([
            'create'    => (int) $create,
            'subscribe' => (int) $subscribe
        ]);
    }

    /**
     * GetModule
     *
     * @return array
     */
    public function getModuleList()
    {
        if ($this->_vortexiqModules === null) {
            $this->_vortexiqModules = [];

            $allowList = true;
            $hostName = $this->_urlBuilder->getBaseUrl();
            foreach (self::DEV_ENV as $env) {
                if (strpos($hostName, $env) !== false) {
                    $allowList = false;
                    break;
                }
            }

            if ($allowList) {
                $moduleList = $this->_moduleList->getNames();
                foreach ($moduleList as $name) {
                    if (strpos($name, 'Vortexiq_Connect') === false) {
                        continue;
                    }

                    $this->_vortexiqModules[] = $name;
                }
            }
        }

        return $this->_vortexiqModules;
    }
    /**
     * CheckExt
     *
     * @param string $moduleName
     */
    public function checkExtensionStatus($moduleName)
    {
        $configModulePath = 'vortexiq_connect';
        $customerEmail = $this->getConfigValue($configModulePath . '_tab/module/email');
        $baseUrl = $this->getConfigValue('web/unsecure/base_url');
        $host = parse_url($baseUrl, PHP_URL_HOST); // phpcs:ignore
        $domain = preg_replace('/^(\.)/i', '', $host);

        $params = [
            'extension' => $moduleName,
            'customer_email' => $customerEmail,
            'domain' => $domain,
            'is_validate' => 1
        ];
        $activateModel = $this->activateFactory->create();
        $result = $activateModel->activate($params);

        if (isset($result['active']) && $result['active'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}
