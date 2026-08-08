<?php

namespace WdevAmar\LogViewer\Model;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\LocalizedException;

class LogFileDeleter
{
    protected $fileDriver;

    public function __construct(
        File $fileDriver
    ) {
        $this->fileDriver = $fileDriver;
    }

    public function deleteFile(string $filePath): bool
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__("Log file does not exist."));
        }

        try {
            return $this->fileDriver->deleteFile($filePath);
        } catch (\Exception $e) {
            throw new LocalizedException(__("Could not delete log file: %1", $e->getMessage()));
        }
    }
}
