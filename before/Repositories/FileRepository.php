<?php

namespace Repositories;

use Models\FileModel;
use PDO;

class FileRepository extends BaseRepository
{

    /**
     * @return array
     */
    public function getFilesList(): array
    {
        $sql = "SELECT * FROM `files` ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $filesData = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($filesData as $fileData) {
            $fileModel = fileModel::create($fileData['file_name'], json_decode($fileData['shared_users']));
            $fileModel->setId($fileData['id']);
            $result[] = $fileModel;
        }
        return $result;
    }

    /**
     * @param int $id
     * @return FileModel
     */
    public function getFileById(int $id): FileModel
    {
        $sql = "SELECT * FROM `files` where `id` = {$id}";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $fileData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($fileData['shared_users'] === null) {
            $fileModel = FileModel::create($fileData['file_name'], null);
            $fileModel->setId($fileData['id']);
            return $fileModel;
        }
        $fileModel = FileModel::create($fileData['file_name'], json_decode($fileData['shared_users']));
        $fileModel->setId($fileData['id']);
        return $fileModel;
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function saveFile(string $fileName): bool
    {
        $sql = "INSERT INTO `files` (file_name) VALUES ('{$fileName}')";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute();
    }
}