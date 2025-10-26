<?php

declare(strict_types=1);

namespace App\Components;

use App\Entities\AbstractEntity;

/**
 * Class IdentityMap
 * @package App\Components
 */
class IdentityMap
{
    private static array $map = [];

    /**
     * @param AbstractEntity $entity
     * @return void
     */
    public static function add(AbstractEntity $entity): void
    {
        self::$map[self::getMapEntityKey($entity)] = $entity;
    }

    /**
     * @param string $entityClass
     * @param int|string $id
     * @return AbstractEntity|null
     */
    public static function get(string $entityClass, int|string $id): ?AbstractEntity
    {
        return self::$map[self::generateMapKey($entityClass, $id)] ?? null;
    }

    /**
     * @param AbstractEntity $entity
     * @return void
     */
    public static function delete(AbstractEntity $entity): void
    {
        unset(self::$map[self::getMapEntityKey($entity)]);
    }

    /**
     * @param string $entityClass
     * @param int|string $id
     * @return void
     */
    public static function deleteByClassAndId(string $entityClass, int|string $id): void
    {
        unset(self::$map[self::generateMapKey($entityClass, $id)]);
    }

    /**
     * @param AbstractEntity $entity
     * @return string
     */
    private static function getMapEntityKey(AbstractEntity $entity): string
    {
        return self::generateMapKey($entity->getClassName(), $entity->getId());
    }

    /**
     * @param string $entityClass
     * @param int|string $id
     * @return string
     */
    private static function generateMapKey(string $entityClass, int|string $id): string
    {
        return $entityClass . '.' . $id;
    }
}
