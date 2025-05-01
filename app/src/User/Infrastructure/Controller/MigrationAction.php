<?php
declare(strict_types=1);


namespace App\User\Infrastructure\Controller;

use App\Shared\Infrastructure\Controller\BaseAction;
use App\User\Infrastructure\Database\Db;
use PDOException;

class MigrationAction extends BaseAction
{
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function __invoke()
    {
        try {
            $this->db->connection->beginTransaction();
            $sql = "CREATE TABLE user_user (id VARCHAR(36) UNIQUE NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));";
            $this->db->connection->exec($sql);
            $this->db->connection->commit();

            return $this->responseSuccess(null)->asJson();
        } catch (PDOException $e) {
            $this->db->connection->rollback();
            throw $e;
        }
    }

}