<?php

declare(strict_types=1);


namespace App\Tests\News\Domain\Entity\ValueObject;

use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsTitleTest extends KernelTestCase
{

    #[dataProvider('getDataProvider')]
    public function test_news_title_creation_successfully(
        string $title,
    ): void {
        // Arrange
        $newsTitle = new NewsTitle($title);
        // Act

        // Assert
        $this->assertInstanceOf(NewsTitle::class, $newsTitle);
        $this->assertSame($newsTitle->getValue(), $title);
    }

    #[dataProvider('invalidLinkDataProvider')]
    public function test_news_title_creation_negative(
        string $title,
    ): void {
        // Arrange

        // Act

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('News title should be between 3 and 255 characters.');

        new NewsTitle($title);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => ['hello everybody'];
        yield 'min title' => ['cat'];
        yield 'max title' => ['a29408246075696da63c9c9f88455120f48e5aba65bf9132c91af5e125f34fd3ac5706ef0ccb8d5d681691be22aceddb94db0b5316a6be4ca299ba458cc52059d35ad4e313228e756f53203b2357de20b748957e0267d42bdfbd5ac0b903444bbe49777259c68e9caa2d8f9dd7a47f5c8f3c9d807bf0f83deae4764e0aac7d0'];
    }

    public static function invalidLinkDataProvider(): \Generator
    {
        yield 'empty title' => [''];
        yield 'lower title' => ['qq',];
        yield 'greater title' => ['a29408246075696da63c9c9f88455120f48e5aba65bf9132c91af5e125f34fd3ac5706ef0ccb8d5d681691be22aceddb94db0b5316a6be4ca299ba458cc52059d35ad4e313228e756f53203b2357de20b748957e0267d42bdfbd5ac0b903444bbe49777259c68e9caa2d8f9dd7a47f5c8f3c9d807bf0f83deae4764e0aac7d01',];
    }

}