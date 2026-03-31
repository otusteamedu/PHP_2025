<?php

namespace App\Repository;

use App\Model\Todo;

class InMemoryTodoRepository {
    private array $todos = [];
    private int $idCounter = 1;

    public function getAll(): array {
        return array_values($this->todos);
    }

    public function save(string $title): Todo {
        $todo = new Todo($this->idCounter++, $title);
        $this->todos[$todo->getId()] = $todo;
        return $todo;
    }

    public function delete(int $id): bool {
        if (isset($this->todos[$id])) {
            unset($this->todos[$id]);
            return true;
        }
        return false;
    }
}
