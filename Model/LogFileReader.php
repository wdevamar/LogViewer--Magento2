<?php

namespace WdevAmar\LogViewer\Model;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\LocalizedException;

class LogFileReader
{
    protected $fileDriver;

    public function __construct(
        File $fileDriver
    ) {
        $this->fileDriver = $fileDriver;
    }

    public function readLastLines(string $filePath, int $lines = 100): array
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__("Log file does not exist."));
        }
        if ($lines <= 0) {
            throw new LocalizedException(__("Number of lines must be a positive integer."));
        }

        $handle = $this->fileDriver->fileOpen($filePath, 'r');
        $buffer = 4096; // Read in 4KB chunks
        $lineCount = 0;
        $output = [];

        // Go to the end of the file
        $this->fileDriver->fileSeek($handle, 0, SEEK_END);
        $pos = $this->fileDriver->fileTell($handle);

        while ($pos > 0 && $lineCount < $lines) {
            $pos = max(0, $pos - $buffer);
            $this->fileDriver->fileSeek($handle, $pos);
            $chunk = $this->fileDriver->fileRead($handle, $buffer);

            // Split by lines and reverse to process from bottom up
            $chunkLines = explode("\n", $chunk);
            $chunkLines = array_reverse($chunkLines);

            foreach ($chunkLines as $line) {
                if ($lineCount < $lines && trim($line) !== '') {
                    $output[] = $line;
                    $lineCount++;
                }
            }
        }

        $this->fileDriver->fileClose($handle);
        return array_reverse($output);
    }

    public function getFileSize(string $filePath): int
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__("Log file does not exist."));
        }
        return $this->fileDriver->stat($filePath)['size'];
    }

    public function getLastModified(string $filePath): string
    {
        if (!$this->fileDriver->isExists($filePath)) {
            throw new LocalizedException(__("Log file does not exist."));
        }
        return date('Y-m-d H:i:s', $this->fileDriver->stat($filePath)['mtime']);
    }
}
