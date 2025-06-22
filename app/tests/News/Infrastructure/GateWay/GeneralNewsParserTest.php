<?php

declare(strict_types=1);

namespace App\Tests\News\Infrastructure\GateWay;

use App\News\Application\GateWay\NewsParserRequest;
use App\News\Application\GateWay\NewsParserResponse;
use App\News\Infrastructure\Gateway\GeneralNewsParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneralNewsParserTest extends KernelTestCase
{

    #[dataProvider('getDataProvider')]
    public function testGetTitle(string $url, string $html): void
    {
        $request = new NewsParserRequest($url);

        $mock = $this->getMockBuilder(GeneralNewsParser::class)
            ->onlyMethods(['getFileContents'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getFileContents')
            ->with($request->url)
            ->willReturn($html);

        $response = $mock->getTitle($request);

        $this->assertInstanceOf(NewsParserResponse::class, $response);
        $this->assertEquals('Тестовый заголовок', $response->title);
    }

    #[dataProvider('getDataProviderNoTitle')]
    public function testGetTitleWithNoTitle(string $url, string $html): void
    {
        $request = new NewsParserRequest($url);

        $mock = $this->getMockBuilder(GeneralNewsParser::class)
            ->onlyMethods(['getFileContents'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getFileContents')
            ->with($request->url)
            ->willReturn($html);

        $response = $mock->getTitle($request);

        $this->assertInstanceOf(NewsParserResponse::class, $response);
        $this->assertEquals('', $response->title);
    }

    public static function getDataProvider(): \Generator
    {
        yield 'standard case' => [
            'https://example.com',
            '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Тестовый заголовок</title>
            </head>
            <body>
                <h1>Заголовок страницы</h1>
            </body>
            </html>'
        ];
    }

    public static function getDataProviderNoTitle(): \Generator
    {
        yield 'standard case' => [
            'https://example.com',
            '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
            </head>
            <body>
                <h1>Заголовок страницы</h1>
            </body>
            </html>'
        ];
    }
}