<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Repository\PaginationResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findById(string $id): ?News
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function save(News $news): void
    {
        $this->getEntityManager()->persist($news);
        $this->getEntityManager()->flush();
    }

    public function findByFilter(NewsFilter $filter): PaginationResult
    {
        $qb = $this->createQueryBuilder('n');
        if ($filter->search) {
            $qb->andWhere('n.title LIKE :search')
                ->setParameter('search', '%' . $filter->search . '%');
        }
        if ($filter->getNewsIds()) {
            $qb->andWhere('n.id IN (:ids)')
                ->setParameter('ids', $filter->getNewsIds());
        }

        if ($filter->pager) {
            $qb->setMaxResults($filter->pager->getLimit());
            $qb->setFirstResult($filter->pager->getOffset());
        }
        $paginator = new Paginator($qb->getQuery());

        return new PaginationResult(iterator_to_array($paginator->getIterator()), $paginator->count());
    }
}
