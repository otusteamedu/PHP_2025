<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findAllWithPagination(int $limit, int $offset): array
    {
        return $this->findBy(
            [],
            [
                'created_at' => 'desc',
                'id' => 'desc',
            ],
            $limit,
            $offset
        );
    }

    public function findOneById(int $id): ?News
    {
        return $this->find($id);
    }

    public function save(News $news): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($news);
        $entityManager->flush();
    }
}
