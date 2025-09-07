<?php

namespace App\Application\Services;

use App\Infrastructure\Repositories\TodoRepository;

class TodoService
{
    public function __construct(
        private TodoRepository $repository
    ) {}

    public function getByUserId(int $userId): array
    {
        $todos = $this->repository->getByUserId($userId);
        return $todos;
    }

    public function getById(int $todoId, int $userId): ?array
    {
        $todo = $this->repository->getById($todoId, $userId);
        return $todo;
    }

    public function add(string $content, int $userId): void
    {
        $this->repository->add($content, $userId);
    }

    public function update(string $content, int $todoId, int $userId): void
    {
        $this->repository->update($content, $todoId, $userId);
    }

    public function delete(int $todoId, int $userId): void
    {
        $this->repository->delete($todoId, $userId);
    }
}