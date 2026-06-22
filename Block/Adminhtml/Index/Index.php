<?php
namespace Vortexiq\Connect\Block\Adminhtml\Index;

class Index extends \Magento\Backend\Block\Template
{
    /**
     * @var Logger
     */
    protected $_logger;
    /**
     * @var _connectHelper
     */
    protected $_connectHelper;
    /**
     * @var _validateHelper
     */
    protected $_validateHelper;
    /**
     * Construct
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Vortexiq\Connect\Helper\Data $connectHelper
     * @param \Vortexiq\Connect\Helper\Validate $validateHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Vortexiq\Connect\Helper\Data $connectHelper,
        \Vortexiq\Connect\Helper\Validate $validateHelper,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_connectHelper = $connectHelper;
        $this->_validateHelper = $validateHelper;
        parent::__construct($context, $data);
    }
}
