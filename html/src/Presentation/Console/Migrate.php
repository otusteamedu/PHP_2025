<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Console;

use Otus\Queue\Infrastructure\Database\DatabaseInterface;

final readonly class Migrate
{
    /**
     * @param DatabaseInterface $database
     */
    public function __construct(
        private DatabaseInterface $database,
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $this
            ->database
            ->command('CREATE TABLE IF NOT EXISTS messages (id SERIAL PRIMARY KEY, author VARCHAR(255), text TEXT, created_at BIGINT);');

        return 0;
    }
}
