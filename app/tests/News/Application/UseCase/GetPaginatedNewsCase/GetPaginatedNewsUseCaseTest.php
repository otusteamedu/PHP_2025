<?php

declare(strict_types=1);

namespace App\Tests\News\Application\UseCase\GetPaginatedNewsCase;

use App\News\Application\DTO\NewsDTO;
use App\News\Application\DTO\NewsDTOTransformerInterface;
use App\News\Application\UseCase\GetPaginatedNews\GetPaginatedNewsRequest;
use App\News\Application\UseCase\GetPaginatedNews\GetPaginatedNewsResponse;
use App\News\Application\UseCase\GetPaginatedNews\GetPaginatedNewsUseCase;
use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Repository\Pager;
use App\Shared\Domain\Repository\PaginationResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetPaginatedNewsUseCaseTest extends TestCase
{
    private GetPaginatedNewsUseCase $useCase;
    private NewsRepositoryInterface $newsRepositoryMock;
    private NewsDTOTransformerInterface $transformerMock;

    protected function setUp(): void
    {
        $this->newsRepositoryMock = $this->createMock(NewsRepositoryInterface::class);
        $this->transformerMock = $this->createMock(NewsDTOTransformerInterface::class);

        $this->useCase = new GetPaginatedNewsUseCase(
            $this->transformerMock,
            $this->newsRepositoryMock
        );
    }

    #[dataProvider('getDataProvider')]
    public function testInvokeReturnsCorrectPagination(string $title, string $url, int $totalItems): void
    {
        // Arrange
        $page = 1;
        $perPage = 10;
        $newsItems = [];
        $newsItemsDTOs = [];
        $news = new News(Uuid::v4(), new NewsTitle($title), new NewsLink($url));

        for ($i = 0; $i < $perPage; $i++) {
            $clone = clone $news;
            $dto = new NewsDTO();
            $dto->id = $news->getId()->toString();
            $dto->title = $news->getTitle()->getValue();
            $dto->link = $news->getLink()->getValue();
            $dto->created_at = $news->getCreatedAt();
            $newsItemsDTOs[] = $dto;
            $newsItems[] = $clone;
        }

        $filter = new NewsFilter(new Pager($page, $perPage));

        $this->newsRepositoryMock->expects($this->once())
            ->method('findByFilter')
            ->with($filter)
            ->willReturn(new PaginationResult($newsItems, $totalItems));

        $this->transformerMock->expects($this->once())
            ->method('fromEntityList')
            ->with($newsItems)
            ->willReturn($newsItemsDTOs);

        $request = new GetPaginatedNewsRequest($filter);

        // Act
        $response = ($this->useCase)($request);

        // Assert
        $this->assertInstanceOf(GetPaginatedNewsResponse::class, $response);
        $this->assertEquals($newsItemsDTOs, $response->news);
        $this->assertEquals($page, $response->pager->page);
        $this->assertEquals($perPage, $response->pager->per_page);
        $this->assertEquals($totalItems, $response->pager->total_items);
    }

    public function testInvokeWithEmptyResult(): void
    {
        // Arrange
        $filter = new NewsFilter(new Pager(1, 10));

        $this->newsRepositoryMock->method('findByFilter')
            ->willReturn(new PaginationResult([], 0));

        $this->transformerMock->method('fromEntityList')
            ->willReturn([]);

        $request = new GetPaginatedNewsRequest($filter);

        // Act
        $response = ($this->useCase)($request);

        // Assert
        $this->assertEmpty($response->news);
        $this->assertEquals(0, $response->pager->total);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => [
            'hello everybody',
            'https://example.com/1',
            38
        ];
        yield 'long title' => [
            'Эксперты обсудили доверие к ИИ и прикладные решения',
            'https://example.com/2',
            39
        ];
        yield 'special characters title' => [
            'Title with "quotes" & special chars',
            'https://example.com/special/quotes',
            40
        ];
    }
}