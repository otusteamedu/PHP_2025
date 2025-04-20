<?php

declare(strict_types=1);

namespace Repositories;

use PDO;
use Repositories\Interfaces\DirectoryRepositoryInterface;

class DirectoryRepository extends BaseRepository implements DirectoryRepositoryInterface
{
    /**
     * @param int $id
     * @return array|null
     */
    public function getDirectory(int $id): ?array
    {
        $sql = "SELECT * FROM `directories` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @param string $directoryPath
     * @return bool
     */
    public function saveDirectory(string $directoryPath): bool
    {
        $sql = "INSERT INTO `directories` (`directory_name`) VALUES (:directory_name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':directory_name' => $directoryPath]);
    }
}