<?php
declare(strict_types=1);

namespace App\Database;

interface Database {
    public function query(string $sqlText, ?array $bindings = []): bool;
    public function prepare(string $sqlText): void;
    public function rollback(): bool;
    public function commit(): bool;
    public function begin(): bool;
    public function fetchAll(string $sqlText, ?array $bindings = []): array;
    public function fetch(string $sqlText, ?array $bindings = []): ?array;
    public function fetchValue(string $sqlText, ?array $bindings = []): ?string;
}
