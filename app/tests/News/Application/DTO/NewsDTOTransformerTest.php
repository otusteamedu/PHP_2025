<?php

declare(strict_types=1);

namespace App\Tests\News\Application\DTO;

use App\News\Application\DTO\NewsDTO;
use App\News\Application\DTO\NewsDTOTransformer;
use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class NewsDTOTransformerTest extends TestCase
{
    private NewsDTOTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new NewsDTOTransformer();
    }

    #[dataProvider('getDataProvider')]
    public function testFromEntityTransformsCorrectly(string $title, string $link): void
    {
        // Arrange
        $id = Uuid::v4();
        $createdAt = new \DateTimeImmutable();

        $news = new News(
            $id,
            new NewsTitle($title),
            new NewsLink($link),
        );

        // Act
        $dto = $this->transformer->fromEntity($news);

        // Assert
        $this->assertInstanceOf(NewsDTO::class, $dto);
        $this->assertEquals($id->toString(), $dto->id);
        $this->assertEquals($title, $dto->title);
        $this->assertEquals($link, $dto->link);
        $this->assertGreaterThanOrEqual($createdAt, $dto->created_at);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => [
            'hello everybody',
            'https://example.com/1'
        ];
        yield 'long title' => [
            'Эксперты обсудили доверие к ИИ и прикладные решения',
            'https://example.com/2'
        ];
        yield 'special characters title' => [
            'Title with "quotes" & special chars',
            'https://example.com/special/quotes'
        ];
    }


}