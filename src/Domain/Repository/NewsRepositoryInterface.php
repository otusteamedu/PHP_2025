<?php

namespace Domain\Repository;

use \Domain\Entity\News;

interface NewsRepositoryInterface  
{  
    public function findAll(): iterable;

    public function findById(int $id): ?News;

    public function save(News $url): void;

    public function delete(News $url): void;

    public function report(): void;
}