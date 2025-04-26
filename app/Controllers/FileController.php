<?php

namespace App\Controllers;

use App\Core\Exceptions\FileError;
use App\Service\Interfaces\FileServiceInterface;

class FileController extends BaseController
{
    private FileServiceInterface $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @return array
     */
    public function listFiles(): array
    {
        $fileList = [];
        $files = $this->fileService->getList();
        foreach ($files as $file) {
           $fileList[] = $this->jsonResponse($file->toArray());
        }

        return $fileList;
    }

    /**
     * @param int $id
     * @return false|string
     */
    public function fileInfo(int $id): false|string
    {
        $file = $this->fileService->getFile($id);
        return $this->jsonResponse($file->toArray());
    }

    /**
     * @return false|string
     */
    public function addFile(): false|string
    {
        try {
            $fileData = $this->getUploadedFile();
            if ($fileData['size'] > 2000000000) {
                throw new FileError('File size exceeds 2GB');
            }

            if (!$this->fileService->checkDirectoryExists($fileData['directory_name'])) {
                throw new FileError('Папка ' . $fileData['directory_name'] . ' отсутствует!');
            }

            $this->fileService->addNewFile($fileData['name'], $fileData['tmp'], $fileData['directory_name']);
            $files = $this->fileService->getList();
            $lastFile = array_pop($files);

            return $this->jsonResponse([
                'success' => 'Файл успешно загружен.',
                'file' => [
                    'id' => $lastFile->getId(),
                    'name' => $lastFile->getFileName()
                ]
            ]);
        } catch (FileError $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @return false|string
     */
    public function addDirectory(): false|string
    {
        $result = $this->fileService->addDirectory($this->getRequestData()['directory_name']);

        if ($result === false) {
            return $this->jsonResponse('Папка ' . $this->getRequestData()['directory_name'] . ' уже существует!');
        }

        return $this->jsonResponse('Папка ' . $this->getRequestData()['directory_name'] . ' успешно создана.');
    }

    /**
     * @param int $id
     * @return false|string
     */
    public function directoryInfo(int $id): false|string
    {
        $result = $this->fileService->getDirectoryInfo($id);
        if ($result === []) {
            return $this->jsonResponse('Папка не найдена.');
        }

        return $this->jsonResponse($result);
    }
}