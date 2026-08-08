<?php

namespace WdevAmar\LogViewer\Model;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\LocalizedException;

class LogFileDownloader
{
    protected $fileFactory;
    protected $fileDriver;

    public function __construct(
        FileFactory $fileFactory,
        File $fileDriver
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileDriver = $fileDriver;
    }

    public function downloadFile(string $filePath, string $fileName): ResponseInterface
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__("Log file does not exist."));
        }

        // Use a more robust way to handle file downloads in Magento 2
        return $this->fileFactory->create(
            $fileName,
            [
                'type' => 'filename',
                'value' => $filePath,
                'rm' => false
            ],
            DirectoryList::VAR_DIR
        );
    }
}
