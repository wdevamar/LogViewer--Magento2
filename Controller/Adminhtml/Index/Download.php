<?php

namespace WdevAmar\LogViewer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use WdevAmar\LogViewer\Model\LogFileDownloader;
use WdevAmar\LogViewer\Model\LogFile;
use Magento\Framework\App\ResponseInterface;

class Download extends Action
{
    const ADMIN_RESOURCE = 'WdevAmar_LogViewer::download';

    protected $logFileDownloader;
    protected $logFileModel;

    public function __construct(
        Context $context,
        LogFileDownloader $logFileDownloader,
        LogFile $logFileModel
    ) {
        $this->logFileDownloader = $logFileDownloader;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    public function execute(): ResponseInterface
    {
        $fileName = $this->getRequest()->getParam('file');

        if (!$fileName) {
            $this->messageManager->addErrorMessage(__('Please select a log file to download.'));
            return $this->_redirect('*/*/index');
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
            $this->messageManager->addErrorMessage(__('Invalid log file selected for download.'));
            return $this->_redirect('*/*/index');
        }

        try {
            return $this->logFileDownloader->downloadFile($filePath, $fileName);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('*/*/index');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
