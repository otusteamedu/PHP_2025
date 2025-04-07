<?php declare(strict_types=1);

namespace App\Storage;

interface RedisSpecificOperationsInterface
{
    public function getClient(): \Redis;

    public function zAdd(string $key, array|float $score, mixed $data): int|float|false;
    public function zRevRange(string $key, int $start, int $end, mixed $scores = null): array|false;
}
