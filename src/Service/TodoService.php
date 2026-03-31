<?php

namespace App\Service;

use App\Repository\InMemoryTodoRepository;
use App\Model\Todo;

class TodoService {
    public function __construct(private InMemoryTodoRepository $repo) {}

    public function getTodos(): array {
        return $this->repo->getAll();
    }

    public function addTodo(string $title): Todo {
        $title = trim($title);

        if ($title === '') {
            throw new \InvalidArgumentException("Title cannot be empty");
        }
        return $this->repo->save($title);
    }

    public function deleteTodo(int $id): bool {
        return $this->repo->delete($id);
    }
}
