<?php
namespace Vortexiq\Connect\Model\Config\Structure;

use Magento\Config\Model\Config\Structure\Data as StructureData;
use Vortexiq\Connect\Block\Adminhtml\System\Config\Button;
use Vortexiq\Connect\Block\Adminhtml\System\Config\Form\Field\Version;
use Vortexiq\Connect\Block\Adminhtml\System\Config\Message;
use Vortexiq\Connect\Helper\Validate as Helper;

class Data
{
    /**
     * @var Helper
     */
    protected $_helper; // phpcs:ignore

    /**
     * Data constructor.
     *
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * BeforeMerge
     *
     * @param StructureData $object
     * @param array $config
     *
     * @return array
     */
    public function beforeMerge(StructureData $object, array $config)
    {
        if (!isset($config['config']['system'])) {
            return [$config];
        }

        /** @var array $sections */
        $sections = $config['config']['system']['sections'];
        foreach ($sections as $sectionId => $section) {
            if (isset($section['tab']) && ($section['tab'] === 'vortexiq') && ($section['id'] !== 'vortexiq')) {
                foreach ($this->_helper->getModuleList() as $moduleName) {
                    if ($section['id'] !== 'vortexiq_connect_tab') {
                        continue;
                    }

                    $dynamicGroups = $this->getDynamicConfigGroups($moduleName, $section['id']);

                    if (!empty($dynamicGroups)) {
                        $config['config']['system']['sections']
                        [$sectionId]['children'] = $dynamicGroups + $section['children'];
                    }
                    break;
                }
            }
        }

        return [$config];
    }

    /**
     * GetDynamicConfigGroups
     *
     * @param string $moduleName
     * @param string $sectionName
     *
     * @return mixed
     */
    protected function getDynamicConfigGroups($moduleName, $sectionName)
    {

        $defaultFieldOptions = [
            'type'          => 'text',
            'showInDefault' => '1',
            'showInWebsite' => '0',
            'showInStore'   => '0',
            'sortOrder'     => 1,
            'module_name'   => $moduleName,
            'module_type'   => $this->_helper->getModuleType($moduleName),
            'validate'      => 'required-entry',
            '_elementType'  => 'field',
            'path'          => $sectionName . '/module'
        ];

        $type = $this->_helper->getModuleType($moduleName);
        $fields = [];
        foreach ($this->getFieldList() as $id => $option) {
            $fields[$id] = array_merge($defaultFieldOptions, ['id' => $id], $option); // phpcs:ignore
        }

        return [
            'module' => [
                'id'            => 'module',
                'label'         => __('Module Information'),
                'showInDefault' => '1',
                'showInWebsite' => '0',
                'showInStore'   => '0',
                '_elementType'  => 'group',
                'path'          => $sectionName,
                'children'      => $fields
            ]
        ];
    }

    /**
     * GetFieldList
     *
     * @return array
     */
    protected function getFieldList()
    {
        return [
            'notice'      => [
                'frontend_model' => Message::class,
            ],
            'version'     => [
                'type'           => 'label',
                'label'          => __('Version'),
                'frontend_model' => Version::class,
            ],
            'name'        => [
                'label'          => __('Customer Name'),
                'frontend_class' => 'vortexiq-module-active-field-free vortexiq-module-active-name',
                'show'           => Helper::MODULE_TYPE_FREE
            ],
            'email'       => [
                'label'          => __('Customer Email Id'),
                'validate'       => 'required-entry validate-email',
                'frontend_class' => 'vortexiq-module-active-field-free vortexiq-module-active-email',
                'comment'        =>
                __('Please provide the email used to purchase this extension from https://marketplace.magento.com/'),
                'show'           => Helper::MODULE_TYPE_FREE
            ],
            'product_key' => [
                'label'          => __('Product Key'),
                'frontend_class' => 'vortexiq-module-active-field-key',
                'show'           => Helper::MODULE_TYPE_FREE
            ],
            'button'      => [
                'frontend_model' => Button::class,
                'show'           => Helper::MODULE_TYPE_FREE
            ]
        ];
    }
}
