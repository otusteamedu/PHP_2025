<?php
declare(strict_types=1);

namespace Domain\Catalog\Services\Repository;

use Domain\Catalog\Services\Entity\Service;

interface ServiceRepositoryInterface
{
    /**
     * @return Service[]
     */
    public function findAll(): iterable;

    public function findById(int $id): ?Service;

    public function save(Service $service): void;

    public function delete(Service $service): void;
}