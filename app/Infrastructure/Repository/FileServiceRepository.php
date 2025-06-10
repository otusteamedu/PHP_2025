<?php
declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Catalog\Services\Entity\Service;

class FileServiceRepository
{
    public function findAll(): iterable
    {
        // TODO: Implement findAll() method.
        return [];
    }

    public function findById(int $id): ?Service
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(Service $service): void
    {
        // Имитация сохранения в БД с присваиванием ID
        $reflectionProperty = new \ReflectionProperty(Service::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($service, 1);
    }

    public function delete(Service $service): void
    {
        // TODO: Implement delete() method.
    }
}