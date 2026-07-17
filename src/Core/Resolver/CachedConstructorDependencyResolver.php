<?php

declare(strict_types=1);

namespace App\Core\Resolver;

use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;

class CachedConstructorDependencyResolver implements ConstructorDependencyResolverInterface
{
    private const int DEFAULT_TTL = 0;

    public function __construct(
        private readonly ConstructorDependencyResolverInterface $innerResolver,
        private readonly DependencyCacheInterface $cache,
    ) {
    }

    public function resolve(string $className): array
    {
        $key = DependencyCacheInterface::KEY_PREFIX . $className;

        $cached = $this->cache->get($key);
        if (is_array($cached)) {
            return $cached;
        }

        $deps = $this->innerResolver->resolve($className);
        $this->cache->set($key, $deps, self::DEFAULT_TTL);

        return $deps;
    }
}
