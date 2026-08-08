<?php

namespace WdevAmar\LogViewer\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class LogFile
{
    protected $directoryList;

    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    public function getLogFiles()
    {
        $logDir = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/log';
        $files = [];
        if (is_dir($logDir)) {
            foreach (new \DirectoryIterator($logDir) as $fileInfo) {
                if ($fileInfo->isFile() && !$fileInfo->isDot()) {
                    $files[] = [
                        'name' => $fileInfo->getFilename(),
                        'path' => $fileInfo->getPathname(),
                        'size' => $fileInfo->getSize(),
                        'modified' => $fileInfo->getMTime()
                    ];
                }
            }
        }
        return $files;
    }
}
