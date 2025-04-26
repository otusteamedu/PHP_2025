<?php

namespace App\Repositories;

use App\Models\FileModel;
use App\Repositories\Interfaces\FileRepositoryInterface;
use PDO;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    /**
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getFilesList(int $page = 1, int $perPage = 100): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT id, file_name FROM `files` ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $result = [];
        while ($fileData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $model = FileModel::create($fileData['file_name']);
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
        $model = FileModel::create($fileData['file_name']);
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
