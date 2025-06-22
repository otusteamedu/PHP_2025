<?php

declare(strict_types=1);

namespace App\Tests\News\Infrastructure\Factory;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Infrastructure\Factory\NewsFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class NewsFactoryTest extends TestCase
{
    private ?NewsFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new NewsFactory();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->factory = null;
    }

    #[dataProvider('getDataProvider')]
    public function testNewsCreatedSuccessfully(string $title, string $link)
    {
        // Act
        $news = $this->factory->create($title, $link);

        // Assert
        $this->assertInstanceOf(News::class, $news);

        $this->assertInstanceOf(NewsTitle::class, $news->getTitle());
        $this->assertEquals($title, $news->getTitle()->getValue());

        $this->assertInstanceOf(NewsLink::class, $news->getLink());
        $this->assertEquals($link, $news->getLink()->getValue());

        $this->assertTrue(Uuid::isValid($news->getId()->toRfc4122()));

        $this->assertNotNull($news->getCreatedAt());
    }

    public static function getDataProvider(): \Generator
    {
        yield 'simple values' => [
            'Test News',
            'https://example.com/news1'
        ];

        yield 'long title' => [
            str_repeat('a', 255), // максимальная длина
            'https://example.com/long-title'
        ];

        yield 'complex URL' => [
            'News with complex URL',
            'https://example.com/path?param=value&another=param#fragment'
        ];

        yield 'special chars in title' => [
            'Новости с "спецсимволами"',
            'https://example.com/special-chars?test=1#fragment'
        ];
    }
}