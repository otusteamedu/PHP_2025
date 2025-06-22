<?php

declare(strict_types=1);

namespace App\Tests\News\Domain\Entity\ValueObject;

use App\News\Domain\Entity\ValueObject\NewsLink;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NewsLinkTest extends TestCase
{
    #[dataProvider('getDataProvider')]
    public function testNewsLinkCreationSuccessfully(
        string $link,
    ): void {
        // Arrange
        $newsLink = new NewsLink($link);

        // Act

        // Assert
        $this->assertInstanceOf(NewsLink::class, $newsLink);
        $this->assertSame($newsLink->getValue(), $link);
    }

    #[dataProvider('invalidLinkDataProvider')]
    public function testNewsLinkCreationNegative(
        string $link,
    ): void {
        // Arrange

        // Act

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('News link is not a valid URL.');

        new NewsLink($link);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => ['https://symfony.com/packages/HttpFoundation'];
        yield 'long title' => ['https://otus.ru/learning/300046/#/'];
        yield 'complex URL' => ['https://example.com/path?param=value&another=param#fragment'];
    }

    public static function invalidLinkDataProvider(): \Generator
    {
        yield 'empty link' => [''];
        yield 'invalid url format' => ['not-a-valid-url'];
        yield 'missing protocol' => ['example.com/news'];
        yield 'javascript protocol' => ['javascript:alert(1)'];
        yield 'space in url' => ['https://example.com/with space'];
    }

}