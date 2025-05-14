<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Repository\NewsRepositoryInterface;


/**@template T
 * @implements NewsRepositoryInterface<T>
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface, ServiceEntityRepositoryInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    )
    {
        parent::__construct($registry, News::class);
    }

    public function save(News $news): void
    {
        $this->entityManager->persist($news);
        $this->entityManager->flush();
    }

    public function getList(): array
    {
        return $this->entityManager
            ->getRepository(News::class)
            ->findAll();
    }

    public function getByIds(array $arNewsIds):array
    {
        return $this->entityManager->getRepository(News::class)->findBy(['id' => $arNewsIds]);
    }
}
