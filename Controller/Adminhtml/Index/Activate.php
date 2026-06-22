<?php
namespace Vortexiq\Connect\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultInterface;
use Vortexiq\Connect\Helper\AbstractData;
use Vortexiq\Connect\Helper\Validate;
use Vortexiq\Connect\Model\ActivateFactory;

class Activate extends Action
{
    /**
     * @var ActivateFactory
     */
    protected $activateFactory;

    /**
     * @var Config
     */
    protected $resourceConfig;

    /**
     * @var AbstractData
     */
    protected $_validateHelper;

    /**
     * @var string
     */
    protected $_moduleConfigPath;

    /**
     * Application config
     *
     * @var ScopeConfigInterface
     */
    protected $_appConfig;

    /**
     * Activate constructor.
     *
     * @param Context $context
     * @param Config $resourceConfig
     * @param ReinitableConfigInterface $config
     * @param Validate $validateHelper
     * @param ActivateFactory $activateFactory
     */
    public function __construct(
        Context $context,
        Config $resourceConfig,
        ReinitableConfigInterface $config,
        Validate $validateHelper,
        ActivateFactory $activateFactory
    ) {
        $this->activateFactory = $activateFactory;
        $this->resourceConfig = $resourceConfig;
        $this->_appConfig = $config;
        $this->_validateHelper = $validateHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {

        $params = $this->getRequest()->getPost()->toArray();

        if (!isset($params['extension'])) {
            return $this->jsonResponse([
                'success' => false,
                'message' => __('Invalid data.')
            ]);
        }
        $this->_moduleConfigPath = $this->_validateHelper->getConfigModulePath($params['extension']).'_tab';

        $activateModel = $this->activateFactory->create();
        $result = $activateModel->activate($params);

        if ($result['success']) {
            $result['active'] = true;

            $configSave = ['active' => 1];
            if (isset($result['key']) && $result['key']) {
                $configSave['product_key'] = $result['key'];
            }
            $otherInfo = [
                    'email'     => $params['customer_email'],
                    'name'     => $params['name'],
                ];
            foreach ($otherInfo as $code => $value) {
                $this->saveConfig('module/' . $code, $value, true);
            }

            $configSave += $otherInfo;

            $this->saveConfig($configSave);
            $this->_appConfig->reinit();
        }

        return $this->jsonResponse($result);
    }

    /**
     * Function
     *
     * @param mixed $result
     *
     * @return mixed
     */
    protected function jsonResponse($result)
    {
        return $this->getResponse()->representJson(
            Validate::jsonEncode($result)
        );
    }

    /**
     * Function
     *
     * @param int $pathId
     * @param mixed $value
     * @param bool $isFullPath
     * @param string $scope
     * @param int $scopeId
     *
     * @return $this
     */
    protected function saveConfig($pathId, $value = null, $isFullPath = false, $scope = 'default', $scopeId = 0)
    {

        if (is_array($pathId)) {
            foreach ($pathId as $path => $pathValue) {
                $this->saveConfig($path, $pathValue, $isFullPath, $scope, $scopeId);
            }

            return $this;
        }
        $fullpath = $isFullPath ? $pathId : $this->buildConfigPath($pathId);

        $this->resourceConfig->saveConfig(
            $isFullPath ? $pathId : $this->buildConfigPath($pathId),
            $value,
            $scope,
            $scopeId
        );

        return $this;
    }

    /**
     * Function
     *
     * @param int $pathId
     *
     * @return string
     */
    protected function buildConfigPath($pathId)
    {
        return $this->_moduleConfigPath . '/module/' . $pathId;
    }

    /**
     * Function
     *
     * @param int $pathId
     * @param bool $isFullPath
     * @param string $scope
     * @param int $scopeId
     *
     * @return $this
     */
    protected function deleteConfig($pathId, $isFullPath = false, $scope = 'default', $scopeId = 0)
    {
        if (is_array($pathId)) {
            foreach ($pathId as $path) {
                $this->deleteConfig($path, $isFullPath, $scope, $scopeId);
            }

            return $this;
        }

        $this->resourceConfig->deleteConfig(
            $isFullPath ? $pathId : $this->buildConfigPath($pathId),
            $scope,
            $scopeId
        );

        return $this;
    }
}
