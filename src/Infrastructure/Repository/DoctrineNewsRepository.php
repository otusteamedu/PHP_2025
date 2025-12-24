<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\UseCase\SubmitNews\NewsAlreadyExistsException;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;
use App\Infrastructure\Entity\NewsRecord;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNewsRepository implements NewsRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAll(): iterable
    {
        $records = $this->em->getRepository(NewsRecord::class)->findAll();

        foreach ($records as $record) {
            yield $this->mapRecordToDomainEntity($record);
        }
    }

    public function findById(int $id): ?News
    {
        $record = $this->em->getRepository(NewsRecord::class)->find($id);

        if (!$record) return null;

        return new News(new Url($record->url), new Title($record->title), $record->createdAt);
    }

    public function findByIds(array $ids): iterable
    {
        $records = $this->em->getRepository(NewsRecord::class)
            ->createQueryBuilder('n')
            ->where('n.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        foreach ($records as $record) {
            yield $this->mapRecordToDomainEntity($record);
        }
    }

    public function save(News $news): void
    {
        $record = new NewsRecord();
        $record->url = $news->getUrl()->getValue();
        $record->title = $news->getTitle()->getValue();
        $record->createdAt = $news->getCreatedAt();

        try {
            $this->em->persist($record);
            $this->em->flush();

            $newsReflection = new \ReflectionClass($news);
            $idProperty = $newsReflection->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($news, $record->id);

        } catch (UniqueConstraintViolationException $e) {
            throw new NewsAlreadyExistsException('News with this URL already exists');
        }
    }

    public function delete(News $news): void
    {
    }

    private function mapRecordToDomainEntity(NewsRecord $record): News
    {
        $news = new News(new Url($record->url), new Title($record->title), $record->createdAt);

        $reflection = new \ReflectionClass($news);
        $idProp = $reflection->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($news, $record->id);

        return $news;
    }
}
