<?php
declare(strict_types=1);


namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Repository\PaginationResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findById(string $id): ?News
    {
        // TODO: Implement findById() method.
    }

    public function add(News $news): void
    {
        $this->getEntityManager()->persist($news);
        $this->getEntityManager()->flush();
    }

    public function findByFilter(NewsFilter $filter): PaginationResult
    {
        // TODO: Implement findAll() method.
    }
}