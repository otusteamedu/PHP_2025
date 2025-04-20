<?php

namespace Service;

use Models\FileModel;
use Repositories\Interfaces\DirectoryRepositoryInterface;
use Repositories\Interfaces\FileRepositoryInterface;
use Service\Interfaces\FileServiceInterface;

class FileService implements FileServiceInterface
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private DirectoryRepositoryInterface $directoryRepository
    ) {}

    /**
     * @return array
     */
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
     * @return bool
     */
    public function addNewFile(string $name, string $tmp): bool
    {
        $filePath = 'files/' . uniqid() . '-' . $name;
        if (!move_uploaded_file($tmp, $filePath)) {
            throw new \RuntimeException('Failed to move uploaded file');
        }
        return $this->fileRepository->saveFile($filePath);
    }

    /**
     * @param string $directoryName
     * @return bool
     */
    public function addDirectory(string $directoryName): bool
    {
        if (file_exists("files/{$directoryName}")) {
            echo 'Папка с таким именем уже существует!';
        } else {
            mkdir("files/{$directoryName}", 0777);
        }
        return $this->directoryRepository->saveDirectory('files/' . $directoryName);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getDirectoryInfo(int $id): array
    {
        $directoryName = $this->directoryRepository->getDirectory($id)['directory_name'];
        $directoryContent = scandir($directoryName);
        $directoryFiles = [];
        foreach ($directoryContent as $content) {
            if ($content === '.' || $content === '..') {
                continue;
            } else {
                $directoryFiles[] = $content;
            }
        }
        return [
            'directory_name' => $directoryName,
            'directory_files' => $directoryFiles
        ];
    }
}