<?php
declare(strict_types=1);

use App\EventSystem\EventSystemRepository;
use App\EventSystem\Redis\RedisEventSystemRepository;
use function DI\autowire;

return [
    EventSystemRepository::class => autowire(RedisEventSystemRepository::class),
];
