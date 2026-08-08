<?php

namespace WdevAmar\LogViewer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use WdevAmar\LogViewer\Model\LogFileReader;
use WdevAmar\LogViewer\Model\LogFile;

class View extends Action
{
    const ADMIN_RESOURCE = 'WdevAmar_LogViewer::view';

    protected $jsonResultFactory;
    protected $logFileReader;
    protected $logFileModel;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        LogFileReader $logFileReader,
        LogFile $logFileModel
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->logFileReader = $logFileReader;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $fileName = $this->getRequest()->getParam('file');
        $lines = (int)$this->getRequest()->getParam('lines', 100);

        if (!$fileName) {
            return $result->setData(['error' => true, 'message' => __('Please select a log file.')]);
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
            return $result->setData(['error' => true, 'message' => __('Invalid log file selected.')]);
        }

        try {
            $content = $this->logFileReader->readLastLines($filePath, $lines);
            $fileSize = $this->logFileReader->getFileSize($filePath);
            $lastModified = $this->logFileReader->getLastModified($filePath);

            return $result->setData([
                'error' => false,
                'content' => implode("\n", $content),
                'file_size' => $fileSize,
                'last_modified' => $lastModified,
                'message' => __('Log file loaded successfully.')
            ]);
        } catch (\Exception $e) {
            return $result->setData(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
