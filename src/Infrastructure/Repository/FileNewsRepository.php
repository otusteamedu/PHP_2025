<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;

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

    public function save(News $news)
    {
        // TODO: Implement save() method.
    }

    public function delete(News $news)
    {
        // TODO: Implement delete() method.
    }
}
