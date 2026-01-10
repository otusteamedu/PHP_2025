<?php

namespace App\Service;

use PDO;

class DBMysql extends DB
{
    public PDO $pdo;

    public function __construct() {
        $this->host = getenv('MYSQL_HOST');
        $this->password = getenv('MYSQL_PASS');
        $this->user = getenv('MYSQL_USER');
        $this->dbname = getenv('MYSQL_DB');

        $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
    }

    public function fetchFirst() {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table LIMIT 1");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll(): ?array {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch(int $id): ?array {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table WHERE id = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $data
     * @return false|string
     */
    public function create(array $data) {
        $columns = array_keys($data);

        $sql = "INSERT INTO $this->table (";
        $strColumns = "";

        foreach ($columns as $column) {
            if ($column === 'id') {
                continue;
            }

            $strColumns .= ":$column, ";
        }

        $strColumns = substr($strColumns, 0, -2);
        $sql .= str_replace(':', '', $strColumns) . ") VALUES ($strColumns)";

        $statement = $this->pdo->prepare($sql);

        foreach ($data as $col => $value) {
            $statement->bindParam(":$col", $value);
        }

        if ($statement->execute()) {
            $result = $this->pdo->lastInsertId($this->table);
        }

        return $result ?? false;
    }

    public function update(array $data): bool {
        $columns = array_keys($data);

        $sql = "UPDATE $this->table SET ";
        $strColumns = "";

        foreach ($columns as $column) {
            if ($column === 'id') {
                continue;
            }

            $strColumns .= "$column = :$column, ";
        }

        $strColumns = substr($strColumns, 0, -2);
        $sql .= "$strColumns WHERE id = :id";

        $statement = $this->pdo->prepare($sql);

        foreach ($data as $col => $value) {
            $statement->bindParam(":$col", $value);
        }
        $statement->bindParam(":id", $data['id']);

        return $statement->execute();
    }

    public function delete(int $id): bool {
        $statement = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $statement->bindParam(':id', $id);
        return $statement->execute();
    }

    /**
     * Чисто для тестов
     *
     * @return bool
     */
    public function createTableForTest(): bool {
        $statement = $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `$this->table` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255)
        )");

        return $statement->execute();
    }
}