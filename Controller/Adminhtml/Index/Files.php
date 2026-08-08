<?php

namespace WdevAmar\LogViewer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use WdevAmar\LogViewer\Model\LogFile;

class Files extends Action
{
    protected $jsonResultFactory;
    protected $logFileModel;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        LogFile $logFileModel
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->logFileModel = $logFileModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $logFiles = $this->logFileModel->getLogFiles();
        $options = [];
        foreach ($logFiles as $file) {
            $options[] = [
                'value' => $file['name'],
                'label' => $file['name'] . ' (' . $this->formatSize($file['size']) . ')'
            ];
        }
        return $result->setData($options);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WdevAmar_LogViewer::view');
    }

    private function formatSize(int $bytes, int $precision = 2): string
    {
        $units = ["B", "KB", "MB", "GB", "TB"];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . " " . $units[$pow];
    }
}
