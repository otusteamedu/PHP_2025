<?php
declare(strict_types=1);

namespace App\Redis;

use function getenv;

final readonly class Config
{
    public string $host;
    public int $port;

    public function __construct()
    {
        $this->host = getenv('REDIS_HOST');
        $this->port = (int) getenv('REDIS_PORT');
    }
}
