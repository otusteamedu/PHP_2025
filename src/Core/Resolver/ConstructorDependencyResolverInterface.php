<?php

declare(strict_types=1);

namespace App\Core\Resolver;

interface ConstructorDependencyResolverInterface
{
    /**
     * Возвращает список имён классов (типов), которые требуются конструктору указанного класса.
     */
    public function resolve(string $className): array;
}
