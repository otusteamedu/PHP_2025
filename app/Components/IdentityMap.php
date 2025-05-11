<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Components;

use Zibrov\OtusPhp2025\Entities\AbstractEntity;

class IdentityMap
{
    private static array $map = [];

    public static function add(AbstractEntity $entity): void
    {
        self::$map[self::getMapEntityKey($entity)] = $entity;
    }

    public static function get(string $entityClass, int|string $id): ?AbstractEntity
    {
        return self::$map[self::generateMapKey($entityClass, $id)] ?? null;
    }

    public static function delete(AbstractEntity $entity): void
    {
        unset(self::$map[self::getMapEntityKey($entity)]);
    }

    private static function getMapEntityKey(AbstractEntity $entity): string
    {
        return self::generateMapKey($entity->getClassName(), $entity->getId());
    }

    private static function generateMapKey(string $entityClass, int|string $id): string
    {
        return $entityClass . '.' . $id;
    }
}
