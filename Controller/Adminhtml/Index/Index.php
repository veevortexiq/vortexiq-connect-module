<?php
namespace Vortexiq\Connect\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var _messageManager
     */
    protected $_messageManager;
    /**
     * @var _helperValidate
     */
    protected $_helperValidate;
    /**
     * @var _helperData
     */
    protected $_helperData;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Vortexiq\Connect\Helper\Validate $helperValidate
     * @param \Vortexiq\Connect\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Vortexiq\Connect\Helper\Validate $helperValidate,
        \Vortexiq\Connect\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->_helperValidate = $helperValidate;
        $this->_helperData = $helperData;
    }

    /**
     * Excute
     */

    public function execute()
    {
        if (!$this->_helperData->isModuleEnabled()
            || !$this->_helperValidate->checkExtensionStatus($this->_helperData::EXTENSION_NAME)) {
            $message = 'Please Activate the License and Module!';
            $this->_messageManager->addError($message);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl()); // phpcs:ignore
            return $resultRedirect;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
