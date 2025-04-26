<?php

namespace App\Service;

use App\Models\FileModel;
use App\Repositories\Interfaces\DirectoryRepositoryInterface;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Service\Interfaces\FileServiceInterface;
use RuntimeException;

readonly class FileService implements FileServiceInterface
{
    public function __construct(
        private FileRepositoryInterface      $fileRepository,
        private DirectoryRepositoryInterface $directoryRepository
    )
    {}


    public function getList(): array
    {
        return $this->fileRepository->getFilesList();
    }

    /**
     * @param int $id
     * @return FileModel
     */
    public function getFile(int $id): FileModel
    {
        return $this->fileRepository->getFileById($id);
    }

    /**
     * @param string $name
     * @param string $tmp
     * @param string|null $directory
     * @return bool
     */
    public function addNewFile(string $name, string $tmp, ?string $directory): bool
    {
        if ($directory === null) {
            $filePath = 'files/' . uniqid() . '-' . $name;
        } else {
            $filePath = 'files/' . $directory . '/' . uniqid() . '-' . $name;
        }

        if (!move_uploaded_file($tmp, $filePath)) {
            throw new RuntimeException('Failed to move uploaded file');
        }
        return $this->fileRepository->saveFile($filePath);
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function checkDirectoryExists(string $directory): bool
    {
        $dirInfo = scandir(__DIR__ . '/../../files');
        foreach ($dirInfo as $dir) {
            if ($dir === $directory) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $directoryName
     * @return bool
     */
    public function addDirectory(string $directoryName): bool
    {
        if (file_exists("files/{$directoryName}")) {
            return false;
        } else {
            mkdir("files/{$directoryName}");
        }

        return $this->directoryRepository->saveDirectory('files/' . $directoryName);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getDirectoryInfo(int $id): array
    {
        $directory = $this->directoryRepository->getDirectory($id);
        if ($directory) {
            $directoryContent = scandir($directory['directory_name']);
            $directoryFiles = [];
            foreach ($directoryContent as $content) {
                if ($content === '.' || $content === '..') {
                    continue;
                } else {
                    $directoryFiles[] = $content;
                }
            }
            return [
                'directory_name' => $directory['directory_name'],
                'directory_files' => $directoryFiles
            ];
        }

        return [];
    }
}