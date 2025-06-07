<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\News;

interface NewsRepositoryInterface
{
    /**
     * @return News[]
     */
    public function findAll(): iterable;
    public function findById(int $id): ?News;
    /**
     * @return News[]
     */
    public function findByIds(array $ids): iterable;
    public function save(News $news);
    public function delete(News $news);
}
