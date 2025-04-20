<?php

namespace Service;

use Models\FileModel;
use Repositories\DirectoryRepository;
use Repositories\FileRepository;

class FileService
{
    public int $id;

    public function __construct(
        private ?FileRepository $fileRepository = null,
        private ?DirectoryRepository $directoryRepository = null)
    {

    }

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->getFileRepository()->getFilesList();
    }

    /**
     * @param int $id
     * @return FileModel
     */
    public function getFile(int $id): FileModel
    {
        return $this->getFileRepository()->getFileById($id);
    }

    /**
     * @param string $name
     * @param string $tmp
     * @return bool
     */
    public function addNewFile(string $name, string  $tmp): bool
    {
        $file = 'files/' . uniqid() . '-' . $name;
        move_uploaded_file($tmp, $file);
        return $this->getFileRepository()->saveFile($file);
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
        return $this->getDirectoryRepository()->saveDirectory('files/' . $directoryName);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getDirectoryInfo(int $id): array
    {
        $directoryName = $this->getDirectoryRepository()->getDirectory($id)['directory_name'];
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

    /**
     * @return FileRepository
     */
    private function getFileRepository(): FileRepository
    {
        if ($this->fileRepository === null) {
            $this->fileRepository = new FileRepository();
        }
        return $this->fileRepository;

    }

    /**
     * @return DirectoryRepository
     */
    private function getDirectoryRepository(): DirectoryRepository
    {
        if ($this->directoryRepository === null) {
            $this->directoryRepository = new DirectoryRepository();
        }
        return $this->directoryRepository;
    }
}
