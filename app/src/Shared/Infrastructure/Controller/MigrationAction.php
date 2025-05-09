<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Shared\Infrastructure\Database\Db;
use App\Shared\Infrastructure\Http\Response;
use PDOException;

class MigrationAction extends BaseAction
{

    public function __construct(private readonly Db $db)
    {
    }

    public function __invoke(): Response
    {
        try {
            $this->db->connection->beginTransaction();
            $sql = "CREATE SCHEMA IF NOT EXISTS public;";
            $this->db->connection->exec($sql);
            //user пусть будет
            $sql = "CREATE TABLE IF NOT EXISTS user_user (id VARCHAR(36) UNIQUE NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));";
            $this->db->connection->exec($sql);
            $sql = "CREATE TABLE IF NOT EXISTS food_food_order (id VARCHAR(36) UNIQUE NOT NULL, status VARCHAR(255), status_created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status_updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));";
            $this->db->connection->exec($sql);
            $sql = "CREATE TABLE IF NOT EXISTS food_food (
                    id VARCHAR(36) UNIQUE NOT NULL, title VARCHAR(255), type VARCHAR(255) NOT NULL, status VARCHAR(255), status_created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status_updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                    ingredients json, order_id VARCHAR(36) NOT NULL, PRIMARY KEY(id));";
            $this->db->connection->exec($sql);
            $sql ="ALTER TABLE food_food ADD CONSTRAINT FK_food_order FOREIGN KEY (order_id) REFERENCES food_food_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE";
            $this->db->connection->exec($sql);

            $this->db->connection->commit();

            return $this->responseSuccess(null)->asJson();
        } catch (PDOException $e) {
            $this->db->connection->rollback();
            throw $e;
        }
    }

}