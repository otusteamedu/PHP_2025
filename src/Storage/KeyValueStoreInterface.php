<?php declare(strict_types=1);

namespace App\Storage;

interface KeyValueStoreInterface
{
    public function set(string $key, string $value): bool;
    public function get(string $key): ?string;
    public function delete(string $key): bool;
    public function exists(string $key): bool;
}
