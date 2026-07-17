<?php

declare(strict_types=1);

namespace App\Core\Resolver;

class ConstructorDependencyResolver implements ConstructorDependencyResolverInterface
{
    public function resolve(string $className): array
    {
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $params = $constructor->getParameters();

        $deps = [];
        foreach ($params as $param) {
            $deps[] = $this->getParameterTypeName($param);
        }

        return $deps;
    }

    private function getParameterTypeName(\ReflectionParameter $param): string
    {
        $type = $param->getType();

        if ($type === null) {
            throw new \LogicException(
                sprintf(
                    "Parameter '%s' in %s has no type hint.",
                    $param->getName(),
                    $param->getDeclaringClass()->getName(),
                ),
            );
        }

        if (!$type instanceof \ReflectionNamedType) {
            $typeName = $this->getTypeDescription($type);

            throw new \LogicException(
                sprintf(
                    "Parameter '%s' in %s has unsupported type: %s",
                    $param->getName(),
                    $param->getDeclaringClass()->getName(),
                    $typeName,
                ),
            );
        }

        return $type->getName();
    }

    private function getTypeDescription(mixed $type): string
    {
        return match (true) {
            $type instanceof \ReflectionUnionType => 'UnionType',
            $type instanceof \ReflectionIntersectionType => 'IntersectionType',
            default => get_class($type),
        };
    }
}
