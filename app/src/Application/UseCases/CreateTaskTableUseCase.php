<?php

declare(strict_types=1);

namespace App\Application\UseCases;
use App\Infrastructure\Database\Migration\TaskTable;
use PDO;

readonly class CreateTaskTableUseCase
{
    public function __construct(
        private PDO $pdo
    )
    {
    }

    public function execute(): void
    {
        $table = new TaskTable($this->pdo);
        $table->ensureExists();
    }
}
