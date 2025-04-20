<?php

namespace Repositories;

use Models\FileModel;
use PDO;
use Repositories\Interfaces\FileRepositoryInterface;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    /**
     * @return array
     */
    public function getFilesList(): array
    {
        $sql = "SELECT * FROM `files`";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $filesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($filesData as $fileData) {
            $model = FileModel::create($fileData['file_name'], json_decode($fileData['shared_users'], true));
            $model->setId($fileData['id']);
            $result[] = $model;
        }
        return $result;
    }

    /**
     * @param int $id
     * @return FileModel|null
     */
    public function getFileById(int $id): ?FileModel
    {
        $sql = "SELECT * FROM `files` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $fileData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fileData) {
            return null;
        }
        $model = FileModel::create($fileData['file_name'], json_decode($fileData['shared_users'], true));
        $model->setId($fileData['id']);
        return $model;
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function saveFile(string $fileName): bool
    {
        $sql = "INSERT INTO `files` (`file_name`) VALUES (:file_name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':file_name' => $fileName]);
    }
}
