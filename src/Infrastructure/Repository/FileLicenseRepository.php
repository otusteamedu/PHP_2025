<?php
declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Licenses\Entity\License;

class FileLicenseRepository
{
    public function findAll(): iterable
    {
        // TODO: Implement findAll() method.
        return [];
    }

    public function findById(int $id): ?License
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(License $license): void
    {
        // Имитация сохранения в БД с присваиванием ID
        $reflectionProperty = new \ReflectionProperty(License::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($license, 1);
    }

    public function delete(License $license): void
    {
        // TODO: Implement delete() method.
    }
}