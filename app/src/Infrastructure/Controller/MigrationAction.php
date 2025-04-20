<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Infrastructure\Database\Db;
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
            $sql = "CREATE TABLE user_post (id VARCHAR(36) NOT NULL, title VARCHAR(100) NOT NULL, content VARCHAR(500) DEFAULT NULL, owner_id VARCHAR(36) NOT NULL, PRIMARY KEY(id));";
            $this->db->connection->exec($sql);
            $sql = "ALTER TABLE user_post ADD CONSTRAINT FK_owner_id FOREIGN KEY (owner_id) REFERENCES user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE;";
            $this->db->connection->exec($sql);
            $this->db->connection->commit();

            return $this->responseSuccess(null)->asJson();
        } catch (PDOException $e) {
            $this->db->connection->rollback();
            throw $e;
        }
    }

}