<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Redis;

use Predis\Response\Status;

interface StorageInterface
{
    public function set(string $key, mixed $value, $expireResolution = null, $expireTTL = null, $flag = null): ?Status;

    public function get(string $key): string;

    public function del(string|array $key): int;

    public function keys(string $pattern): array;

    public function sadd(string $key, array $members): int;
}
