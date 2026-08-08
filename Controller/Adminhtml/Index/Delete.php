<?php

namespace WdevAmar\LogViewer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use WdevAmar\LogViewer\Model\LogFileDeleter;
use WdevAmar\LogViewer\Model\LogFile;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'WdevAmar_LogViewer::delete';

    protected $jsonResultFactory;
    protected $logFileDeleter;
    protected $logFileModel;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        LogFileDeleter $logFileDeleter,
        LogFile $logFileModel
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->logFileDeleter = $logFileDeleter;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $fileName = $this->getRequest()->getParam('file');

        if (!$fileName) {
            return $result->setData(['error' => true, 'message' => __('Please select a log file to delete.')]);
        }

        // Basic validation to prevent directory traversal
        $logFiles = $this->logFileModel->getLogFiles();
        $filePath = null;
        foreach ($logFiles as $logFile) {
            if ($logFile['name'] === $fileName) {
                $filePath = $logFile['path'];
                break;
            }
        }

        if (!$filePath) {
            return $result->setData(['error' => true, 'message' => __('Invalid log file selected for deletion.')]);
        }

        try {
            $this->logFileDeleter->deleteFile($filePath);
            return $result->setData(['error' => false, 'message' => __('Log file "%1" has been deleted.', $fileName)]);
        } catch (\Exception $e) {
            return $result->setData(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
