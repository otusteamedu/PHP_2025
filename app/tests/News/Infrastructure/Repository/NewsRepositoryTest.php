<?php

declare(strict_types=1);


namespace App\Tests\News\Infrastructure\Repository;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Domain\Repository\NewsFilter;
use App\News\Infrastructure\Repository\NewsRepository;
use App\Shared\Domain\Repository\PaginationResult;
use App\Shared\Infrastructure\Kernel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class NewsRepositoryTest extends KernelTestCase
{
    private NewsRepository $repository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->repository = new NewsRepository(self::getContainer()->get('doctrine'));

        // Start transaction
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Rollback transaction
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        parent::tearDown();
    }

    public function testFindById(): void
    {
        // Arrange
        $news = new News(Uuid::v4(), new NewsTitle('Test News'), new NewsLink('https://example.com'));
        $this->entityManager->persist($news);
        $this->entityManager->flush();

        // Act
        $foundNews = $this->repository->findById($news->getId()->toString());

        // Assert
        $this->assertNotNull($foundNews);
        $this->assertEquals($news->getId()->toString(), $foundNews->getId()->toString());
    }

    public function testFindByFilterWithEmptyResult(): void
    {
        // Arrange
        $filter = new NewsFilter();

        // Act
        $result = $this->repository->findByFilter($filter);

        // Assert
        $this->assertCount(0, $result->items);
        $this->assertEquals(0, $result->total);
    }

    public function testSave(): void
    {
        // Arrange
        $news = new News(Uuid::v4(), new NewsTitle('Test Save'), new NewsLink('https://example.com/save'));

        // Act
        $this->repository->save($news);

        // Assert
        $foundNews = $this->repository->findById($news->getId()->toString());
        $this->assertNotNull($foundNews);
    }

    public function testFindByFilterWithSearch(): void
    {
        // Arrange
        $news1 = new News(Uuid::v4(), new NewsTitle('Important News'), new NewsLink('https://example.com/important'));
        $news2 = new News(Uuid::v4(), new NewsTitle('Regular Update'), new NewsLink('https://example.com/regular'));

        $this->entityManager->persist($news1);
        $this->entityManager->persist($news2);
        $this->entityManager->flush();

        // Act
        $filter = new NewsFilter(null, 'Important');
        $result = $this->repository->findByFilter($filter);

        // Assert
        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertCount(1, $result->items);
        $this->assertEquals($news1->getId()->toString(), $result->items[0]->getId()->toString());
    }

    public function testFindByFilterWithIds(): void
    {
        // Arrange
        $news1 = new News(Uuid::v4(), new NewsTitle('News 1'), new NewsLink('https://example.com/1'));
        $news2 = new News(Uuid::v4(), new NewsTitle('News 2'), new NewsLink('https://example.com/2'));

        $this->entityManager->persist($news1);
        $this->entityManager->persist($news2);
        $this->entityManager->flush();

        // Act
        $filter = new NewsFilter();
        $filter->setNewsIds($news1->getId()->toString());
        $result = $this->repository->findByFilter($filter);

        // Assert
        $this->assertCount(1, $result->items);
        $this->assertEquals($news1->getId()->toString(), $result->items[0]->getId()->toString());
    }

}