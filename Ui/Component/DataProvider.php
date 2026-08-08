<?php

namespace WdevAmar\LogViewer\Ui\Component;

use Magento\Ui\DataProvider\AbstractDataProvider;
use WdevAmar\LogViewer\Model\LogFile;

class DataProvider extends AbstractDataProvider
{
    protected $logFileModel;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        LogFile $logFileModel,
        array $meta = [],
        array $data = []
    ) {
        $this->logFileModel = $logFileModel;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $data = ['items' => []];
        $logFiles = $this->logFileModel->getLogFiles();

        foreach ($logFiles as $file) {
            $data['items'][] = [
                'name' => $file['name'],
                'size' => $this->formatSize($file['size']),
                'modified' => date('Y-m-d H:i:s', $file['modified'])
            ];
        }
        $data['totalRecords'] = count($logFiles);
        return $data;
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
