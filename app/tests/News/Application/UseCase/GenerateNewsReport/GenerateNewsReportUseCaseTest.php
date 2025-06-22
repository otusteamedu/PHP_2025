<?php

declare(strict_types=1);


namespace App\Tests\News\Application\UseCase\GenerateNewsReport;

use App\News\Application\DTO\NewsDTO;
use App\News\Application\DTO\NewsDTOTransformerInterface;
use App\News\Application\Service\NewsReportGeneratorInterface;
use App\News\Application\UseCase\GenerateNewsReport\GenerateNewsReportRequest;
use App\News\Application\UseCase\GenerateNewsReport\GenerateNewsReportResponse;
use App\News\Application\UseCase\GenerateNewsReport\GenerateNewsReportUseCase;
use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Repository\PaginationResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\Configuration\File;
use Symfony\Component\Uid\Uuid;

class GenerateNewsReportUseCaseTest extends TestCase
{
    private GenerateNewsReportUseCase $useCase;
    private NewsReportGeneratorInterface $reportGeneratorMock;
    private NewsRepositoryInterface $newsRepositoryMock;
    private NewsDTOTransformerInterface $transformerMock;

    protected function setUp(): void
    {
        $this->reportGeneratorMock = $this->createMock(NewsReportGeneratorInterface::class);
        $this->newsRepositoryMock = $this->createMock(NewsRepositoryInterface::class);
        $this->transformerMock = $this->createMock(NewsDTOTransformerInterface::class);

        $this->useCase = new GenerateNewsReportUseCase(
            $this->reportGeneratorMock,
            $this->newsRepositoryMock,
            $this->transformerMock
        );
    }

    #[dataProvider('getDataProvider')]
    public function testInvokeGeneratesReportSuccessfully(string $title, string $url, string $fileName): void
    {
        //Arrange
        $count = 10;
        $newsItems = [];
        $item = new News(Uuid::v4(), new NewsTitle($title), new NewsLink($url));

        for ($i = 0; $i < $count; $i++) {
            $newsItems[] = clone $item;
        }

        $newsItemsDTO = [];
        foreach ($newsItems as $newsItem) {
            $dto = new NewsDTO();
            $dto->id = $newsItem->getId()->toString();
            $dto->title = $newsItem->getTitle()->getValue();
            $dto->link = $newsItem->getLink()->getValue();
            $dto->created_at = $newsItem->getCreatedAt();
            $newsItemsDTO[] = $dto;
        }
        $newsIds = array_map(function (News $newsItem) {
            return $newsItem->getId()->toString();
        }, $newsItems);

        $filter = new NewsFilter();
        $filter->setNewsIds(...$newsIds);

        $this->newsRepositoryMock->expects($this->once())
            ->method('findByFilter')
            ->with($filter)
            ->willReturn(new PaginationResult($newsItems, count($newsItems)));

        $this->transformerMock->expects($this->once())
            ->method('fromEntityList')
            ->with($newsItems)
            ->willReturn($newsItemsDTO);

        $this->reportGeneratorMock->expects($this->once())
            ->method('generate')
            ->willReturn($fileName);

        //act
        $request = new GenerateNewsReportRequest(...$newsIds);
        $response = ($this->useCase)($request);

        //assert
        $this->assertInstanceOf(GenerateNewsReportResponse::class, $response);
        $this->assertEquals($fileName, $response->pathToFile);
    }

    public function testInvokeGeneratesReportNegative(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('News not found');

        $newsIds = [Uuid::v4()->toString()];

        $this->newsRepositoryMock->method('findByFilter')
            ->willReturn(new PaginationResult([], 0));

        $request = new GenerateNewsReportRequest(...$newsIds);
        ($this->useCase)($request);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => [
            'hello everybody',
            'https://example.com/1',
            'test1.html',
        ];
        yield 'long title' => [
            'Эксперты обсудили доверие к ИИ и прикладные решения',
            'https://example.com/2',
            'test2.html',
        ];
        yield 'special characters title' => [
            'Title with "quotes" & special chars',
            'https://example.com/special/quotes',
            'test3.html',
        ];
    }

}