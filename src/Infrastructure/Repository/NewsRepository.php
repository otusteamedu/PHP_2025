<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\News\ResponseNewsDTO;
use App\Domain\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Repository\NewsRepositoryInterface;


/**@template T
 * @implements NewsRepositoryInterface<T>
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
            $data[] = new ResponseNewsDTO(
                $el->getId(),
                $el->getTitle(),
                $el->getUrl(),
                $el->getCreateDate(),
            );
        }

        return $data;
    }

    public function getByIds(array $arNewsIds):array
    {
        return $this->entityManager->getRepository(News::class)->findBy(['id' => $arNewsIds]);
    }
}
