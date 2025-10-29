<?php

declare(strict_types=1);

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class NewsTest extends TestCase
{
    #[dataProvider('getDataProvider')]
    public function testNewsCreationSuccessfully(
        Uuid $id,
        NewsTitle $title,
        NewsLink $link
    ): void {
        // Arrange

        // Act
        $news = new News($id, $title, $link);

        // Assert
        $this->assertSame($id, $news->getId());
        $this->assertSame($title, $news->getTitle());
        $this->assertSame($link, $news->getLink());

        $this->assertInstanceOf(\DateTimeImmutable::class, $news->getCreatedAt());
        $this->assertLessThanOrEqual(new \DateTimeImmutable(), $news->getCreatedAt());
    }

    public static function getDataProvider(): array
    {
        return [
            'standard case' => [
                Uuid::v4(),
                new NewsTitle('Breaking News'),
                new NewsLink('https://example.com/breaking'),
            ],
            'long title' => [
                Uuid::v4(),
                new NewsTitle(str_repeat('a', 255)), // максимально допустимая длина
                new NewsLink('https://example.com/long'),
            ],
            'special chars in title' => [
                Uuid::v4(),
                new NewsTitle('Новости с "спецсимволами"'),
                new NewsLink('https://example.com/special'),
            ],
            'complex URL' => [
                Uuid::v4(),
                new NewsTitle('Complex URL News'),
                new NewsLink('https://example.com/path?param=value&another=param#fragment'),
            ],
        ];
    }
}