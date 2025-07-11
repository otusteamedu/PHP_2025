<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Infrastructure;

use Camal\AppDataMapperLocal\Domain\Entities\Task;

class TaskIdentityMap
{
    private array $map;

    public function __construct() {}

    /**
     * Сохранить задачу локально
     * @param \Camal\AppDataMapperLocal\Domain\Entities\Task $task
     * @return void
     */
    public function add(Task $task): void
    {
        $this->map[$task->getId()] = $task;
    }

    /**
     * @param int $id
     */
    public function get(int $id): ?Task{
        return $this->map[$id] ?? null;
    }

    /**
     * @param int $id
     * @return void
     */
    public function remove(Task $task): void{
        unset($this->map[$task->getId()]);
    }
}
