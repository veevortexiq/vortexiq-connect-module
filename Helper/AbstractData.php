<?php

/**
 * @author VortexIQ
 * @copyright Copyright (c) vortexiq.ai
 * @package Vortexiq_Connect
 */
namespace Vortexiq\Connect\Helper;

use Exception;
use Magento\Backend\App\Config;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AbstractData extends AbstractHelper
{
    public const CONFIG_MODULE_PATH = 'vortexiq';

    /**
     * @type array
     */
    protected $_data = []; // phpcs:ignore

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $backendConfig;

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * AbstractData constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Function
     *
     * @param mixed $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId);
    }

    /**
     * Function
     *
     * @param string $code
     * @param mixed $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * Function
     *
     * @param string $field
     * @param mixed $storeId
     *
     * @return mixed
     */
    public function getModuleConfig($field = '', $storeId = null)
    {
        $field = ($field !== '') ? '/' . $field : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . $field, $storeId);
    }

    /**
     * Function
     *
     * @param string $field
     * @param mixed $scopeValue
     * @param string $scopeType
     *
     * @return array|mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($scopeValue === null && !$this->isArea()) {
            /** @var Config $backendConfig */
            if (!$this->backendConfig) {
                $this->backendConfig = $this->objectManager->get(\Magento\Backend\App\ConfigInterface::class);
            }

            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * Function
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getData($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        return null;
    }

    /**
     * Function
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setData($name, $value)
    {
        $this->_data[$name] = $value;

        return $this;
    }

    /**
     * Function
     *
     * @return mixed
     */
    public function getCurrentUrl()
    {
        $model = $this->objectManager->get(UrlInterface::class);

        return $model->getCurrentUrl();
    }

    /**
     * Function
     *
     * @param mixed $ver
     * @param string $operator
     *
     * @return mixed
     */
    public function versionCompare($ver, $operator = '>=')
    {
        $productMetadata = $this->objectManager->get(ProductMetadataInterface::class);
        $version = $productMetadata->getVersion(); //will return the magento version

        return version_compare($version, $ver, $operator);
    }

    /**
     * Function
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonEncode($data);
        }

        return $this->getSerializeClass()->serialize($data);
    }

    /**
     * Function
     *
     * @param mixed $string
     *
     * @return mixed
     */
    public function unserialize($string)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonDecode($string);
        }

        return $this->getSerializeClass()->unserialize($string);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public static function jsonEncode($valueToEncode) // phpcs:ignore
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }

    /**
     * Decodes the given $encodedValue string which is encoded in the JSON format
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public static function jsonDecode($encodedValue) // phpcs:ignore
    {
        try {
            $decodeValue = self::getJsonHelper()->jsonDecode($encodedValue);
        } catch (Exception $e) {
            $decodeValue = [];
        }

        return $decodeValue;
    }

    /**
     * Is Admin Store
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isArea(Area::AREA_ADMINHTML);
    }

    /**
     * Function
     *
     * @param string $area
     *
     * @return mixed
     */
    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            /** @var State $state */
            $state = $this->objectManager->get(\Magento\Framework\App\State::class);

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }

    /**
     * Function
     *
     * @param string $path
     * @param array $arguments
     *
     * @return mixed
     */
    public function createObject($path, $arguments = [])
    {
        return $this->objectManager->create($path, $arguments);
    }

    /**
     * Function
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getObject($path)
    {
        return $this->objectManager->get($path);
    }

    /**
     * Function
     *
     * @return JsonHelper|mixed
     */
    public static function getJsonHelper() // phpcs:ignore
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
    }

    /**
     * Function
     *
     * @return mixed
     */
    protected function getSerializeClass()
    {
        return $this->objectManager->get('Zend_Serializer_Adapter_PhpSerialize');
    }
}
