<?php

namespace Repositories;

use PDO;

class DirectoryRepository extends BaseRepository
{
    /**
     * @param string $directoryName
     * @return bool
     */
    public function saveDirectory(string $directoryName): bool
    {
        $sql = "INSERT INTO `directories` (directory_name) VALUES ('{$directoryName}')";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public  function getDirectory(int $id): mixed
    {
        $sql = "SELECT * FROM `directories` WHERE `id` = {$id}";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}