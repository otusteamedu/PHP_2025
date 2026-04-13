<?php

namespace App\Infrastructure\Database\DataMapper;

use App\Domain\Entity\EntityInterface;

abstract class AbstractMapper
{
    public function hydrateId(EntityInterface $entity, int $id): void
    {
        try {
            $entityIdReflectionProperty = new \ReflectionClass($entity)->getProperty('id');
            if ($entityIdReflectionProperty->getValue($entity) === null) {
                $entityIdReflectionProperty->setValue($entity, $id);
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
