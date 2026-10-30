<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

abstract class AbstractMapper implements DataMapperInterface, IdentityMapperInterface
{
    private static array $propertyCache = [];

    public function hydrateId(object $entity, int $id): void
    {
        $property = $this->reflectionProperty($entity::class, 'id');

        if ($property->isInitialized($entity) && $property->getValue($entity) !== null) {
            return;
        }

        $property->setValue($entity, $this->buildIdentity($id));
    }

    protected function hydrate(object $entity, array $properties): void
    {
        foreach ($properties as $property => $value) {
            $this->reflectionProperty($entity::class, $property)->setValue($entity, $value);
        }
    }

    private function reflectionProperty(string $class, string $property): \ReflectionProperty
    {
        try {
            return self::$propertyCache["$class::$property"] ??= new \ReflectionProperty($class, $property);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(
                sprintf('Failed to hydrate property [%s] on entity [%s]: %s', $property, $class, $e->getMessage()),
                previous: $e,
            );
        }
    }

    private function buildIdentity(int $id): mixed
    {
        if ($this instanceof IdentityVoMapperInterface) {
            return $this->createIdentity($id);
        }

        return $id;
    }
}
