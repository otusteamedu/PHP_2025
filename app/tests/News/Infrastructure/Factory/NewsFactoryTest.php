<?php

declare(strict_types=1);

namespace App\Tests\News\Infrastructure\Factory;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Infrastructure\Factory\NewsFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class NewsFactoryTest extends KernelTestCase
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
        $news = $this->factory->create($title, $link);

        // Проверяем тип возвращаемого объекта
        $this->assertInstanceOf(News::class, $news);

        // Проверяем, что заголовок установлен правильно
        $this->assertInstanceOf(NewsTitle::class, $news->getTitle());
        $this->assertEquals($title, $news->getTitle()->getValue());

        // Проверяем, что ссылка установлена правильно
        $this->assertInstanceOf(NewsLink::class, $news->getLink());
        $this->assertEquals($link, $news->getLink()->getValue());

        // Проверяем, что ID является валидным UUID
        $this->assertTrue(Uuid::isValid($news->getId()->toRfc4122()));

        // Проверяем, что дата создания установлена
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