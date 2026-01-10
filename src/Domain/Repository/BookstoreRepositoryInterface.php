<?php

namespace BookstoreApp\Domain\Repository;

use BookstoreApp\Domain\Entity\Bookstore;

interface BookstoreRepositoryInterface
{
    public function findById(int $id): ?Bookstore;
    public function findAll(): array;
    public function findByCity(string $city): array;
    public function save(Bookstore $bookstore): void;
    public function delete(int $id): void;
}