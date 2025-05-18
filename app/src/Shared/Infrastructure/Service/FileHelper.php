<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\FileHelperInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

readonly class FileHelper implements FileHelperInterface
{
    public function __construct(
        private Filesystem $reportFileSystem,
    ) {
    }

    /**
     * @throws FilesystemException
     */
    public function readStream(string $fileName): mixed
    {
        $resource = $this->reportFileSystem->readStream($fileName);
        if (false === $resource) {
            throw new \Exception(sprintf('Не удалось открыть поток "%s"', $fileName));
        }

        return $resource;
    }

    /**
     * @throws FilesystemException
     */
    public function isExist(string $filename): bool
    {
        return $this->reportFileSystem->fileExists($filename);
    }

    public function save(string $content, string $fileName): void
    {
        $result = $this->reportFileSystem->write($fileName, $content);
        if ($result) {
            throw new \Exception("Не удалось загрузить файл: $fileName");
        }
    }

    /**
     * @throws FilesystemException
     */
    public function getFileMimeType(string $filename): string
    {
        return $this->reportFileSystem->mimeType($filename);
    }
}
