<?php

namespace  Controllers;

use Core\Exceptions\FileError;
use Exception;
use Service\FileService;
use Models\FileModel;

class FileController extends BaseController
{
    private ?FileService $fileService = null;

    /**
     * @return FileModel[]
     */
    public function listFiles(): array
    {
        /** @var FileModel[] $fileModels */
        $fileModels = $this->getFileService()->getList();
        $response = [];
        foreach ($fileModels as $model) {
            $response[] = $model->toArray();
        }
        return $response;
    }

    /**
     * @param int $id
     * @return array
     */
    public function fileInfo(int $id): array
    {
        return $this->getFileService()->getFile($id)->toArray();
    }

    /**
     * @return array|bool
     * @throws FileError
     */
    public function addFile(): array|bool
    {
        $data = $this->getDataFiles();
        if ($data['size']  > 2000000000) {
            throw new FileError('Размер файла превышает 2 гигабайта!');
        }
        return $this->getFileService()->addNewFile($data['name'], $data['tmp']);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function addDirectory(): bool
    {
        return $this->getFileService()->addDirectory($this->getRequestData()['directory_name']);
    }

    /**
     * @param int $id
     * @return array
     */
    public function directoryInfo(int $id): array
    {
        return $this->getFileService()->getDirectoryInfo($id);
    }

    /**
     * @return FileService|null
     */
    private function getFileService(): ?FileService
    {
        if ($this->fileService === null) {
            $this->fileService = new FileService();
        }
        return $this->fileService;
    }
}