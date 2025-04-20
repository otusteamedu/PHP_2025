<?php

namespace Controllers;

use Core\Exceptions\FileError;
use Service\Interfaces\FileServiceInterface;

class FileController extends BaseController
{
    private FileServiceInterface $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @return void
     */
    public function listFiles(): void
    {
        $files = $this->fileService->getList();
        $this->jsonResponse($files);
    }

    /**
     * @param int $id
     * @return void
     */
    public function fileInfo(int $id): void
    {
        $file = $this->fileService->getFile($id);
        $this->jsonResponse($file->toArray());
    }

    /**
     * @return void
     */
    public function addFile(): void
    {
        try {
            $fileData = $this->getUploadedFile();
            if ($fileData['size'] > 2000000000) {
                throw new FileError('File size exceeds 2GB');
            }

            $result = $this->fileService->addNewFile($fileData['name'], $fileData['tmp']);
            $this->jsonResponse(['success' => $result]);
        } catch (FileError $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @return bool
     */
    public function addDirectory(): bool
    {
        return $this->fileService->addDirectory($this->getRequestData()['directory_name']);
    }

    /**
     * @param int $id
     * @return array
     */
    public function directoryInfo(int $id): array
    {
        return $this->fileService->getDirectoryInfo($id);
    }
}