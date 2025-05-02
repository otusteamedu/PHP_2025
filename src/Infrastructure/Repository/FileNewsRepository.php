<?php

namespace Infrastructure\Repository;

use \Domain\Repository\NewsRepositoryInterface;
use \Domain\Entity\News;

class FileNewsRepository implements NewsRepositoryInterface
{

    public function findAll(): iterable
    {
        // TODO: Implement findAll() method.
        return [];
    }

    public function findById(int $id): ?News
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(News $News): void
    {
        // Имитация сохранения в БД с присваиванием ID
        $reflectionProperty = new \ReflectionProperty(News::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($News, 1);
    }

    public function delete(News $News): void
    {
        // TODO: Implement delete() method.
    }

    public function report(): void
    {
        // TODO: Implement report() method.
         
    }
}