<?php

namespace App\Shared\Domain\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

readonly class FileHelper
{
    public function __construct(
        private Filesystem $fileSystem
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function readStream(string $fileName)
    {
        $resource = $this->fileSystem->readStream($fileName);
        if ($resource === false) {
            throw new \Exception(sprintf('Не удалось открыть поток "%s"', $fileName));
        }
        return $resource;
    }

    /**
     * @throws FilesystemException
     */
    public function isExist(string $filename): bool
    {
        return $this->fileSystem->fileExists($filename);
    }

    public function save(string $content, string $fileName): void
    {
        $result = $this->fileSystem->write($fileName, $content);
        if ($result) {
            throw new \Exception("Не удалось загрузить файл: $fileName");

        }
    }

    /**
     * @throws FilesystemException
     */
    public function getFileMimeType(string $filename): string
    {
        return $this->fileSystem->mimeType($filename);
    }


}
