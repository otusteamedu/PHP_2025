<?php

namespace App\Domain\Repository;

use App\Domain\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Application\Port\NewsRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

//TODO сам репозиторий в инфраструктуру, а на слое домена только РепозиторийИнтерфейса


/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
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
        $news = $this->entityManager
            ->getRepository(News::class)
            ->findAll();

        $data = [];
        foreach ($news as $el) {
            $data[] = [
                'id' => $el->getId(),
                'title' => $el->getTitle(),
                'url' => $el->getUrl(),
                'create_date' => $el->getCreateDate(),
            ];
        }

        return $data;
    }

    public function getByIds(array $arNewsIds):array
    {
        return $this->entityManager->getRepository(News::class)->findBy(['id' => $arNewsIds]);
    }
}
