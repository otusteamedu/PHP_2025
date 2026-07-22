<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migration;

use PDO;

final readonly class TaskTable
{
    public function __construct(
        private PDO $pdo,
    )
    {
    }

    public function ensureExists(): void
    {
        $this->pdo->exec(<<<'SQL'
            do $$
            begin
                if not exists (
                    select 1
                    from pg_type
                    where typname = 'task_status'
                ) then
                    create type task_status as enum (
                        'new',
                        'queued',
                        'processing',
                        'completed',
                        'failed'
                    );
                end if;
            end $$;
            create table if not exists task
            (
                number integer generated always as identity primary key,
                status task_status not null,
                created_at  timestamp not null
            );
        SQL);
    }
}