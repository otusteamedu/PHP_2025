<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Database;

use Iterator;

interface DatabaseInterface
{
    /**
     * @param string $sql
     * @param array $params
     *
     * @return bool
     */
    public function command(string $sql, array $params): bool;

    /**
     * @param string $sql
     *
     * @return Iterator
     */
    public function query(string $sql): Iterator;
}
