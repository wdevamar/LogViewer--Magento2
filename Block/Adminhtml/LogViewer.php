<?php

namespace WdevAmar\LogViewer\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use WdevAmar\LogViewer\Model\LogFile;
use Magento\Framework\Serialize\Serializer\Json;

class LogViewer extends Template
{
    protected $logFileModel;
    protected $jsonSerializer;

    public function __construct(
        Context $context,
        LogFile $logFileModel,
        Json $jsonSerializer,
        array $data = []
    ) {
        $this->logFileModel = $logFileModel;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
    }

    public function getLogFiles(): array
    {
        return $this->logFileModel->getLogFiles();
    }

    public function getLogFilesJson(): string
    {
        return $this->jsonSerializer->serialize($this->getLogFiles());
    }

    public function getViewUrl(): string
    {
        return $this->getUrl("logviewer/index/view");
    }

    public function getDownloadUrl(): string
    {
        return $this->getUrl("logviewer/index/download");
    }

    public function getDeleteUrl(): string
    {
        return $this->getUrl("logviewer/index/delete");
    }

    public function formatSize(int $bytes, int $precision = 2): string
    {
        $units = ["B", "KB", "MB", "GB", "TB"];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . " " . $units[$pow];
    }
}
