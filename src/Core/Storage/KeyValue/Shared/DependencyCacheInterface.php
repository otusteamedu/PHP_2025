<?php

declare(strict_types=1);

namespace App\Core\Storage\KeyValue\Shared;

interface DependencyCacheInterface extends CacheStorageInterface
{
    public const string KEY_PREFIX = 'resolver_deps:';

    public function clear(): bool;
}
