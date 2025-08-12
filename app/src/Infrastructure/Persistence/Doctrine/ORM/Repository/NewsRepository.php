<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\ORM\Repository;

use App\Domain\Repository\NewsRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\ORM\Entity\News as EntityNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Domain\News;

class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EntityNews::class);
    }

    public function persist(News $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
