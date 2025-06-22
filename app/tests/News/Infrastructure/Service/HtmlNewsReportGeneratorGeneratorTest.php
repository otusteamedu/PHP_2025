<?php

declare(strict_types=1);

namespace App\Tests\News\Infrastructure\Service;

use App\News\Application\DTO\NewsDTO;
use App\News\Infrastructure\Service\HtmlNewsReportGeneratorGenerator;
use App\Shared\Infrastructure\Service\FileHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class HtmlNewsReportGeneratorGeneratorTest extends TestCase
{
    private ?HtmlNewsReportGeneratorGenerator $generator;
    private ?FileHelper $fileHelperMock;

    protected function setUp(): void
    {
        $this->fileHelperMock = $this->createMock(FileHelper::class);
        $this->generator = new HtmlNewsReportGeneratorGenerator($this->fileHelperMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->fileHelperMock = null;
        $this->generator = null;
    }

    #[dataProvider('getDataProvider')]
    public function testGenerateReportWithSingleItem(string $title, string $link): void
    {
        // Arrange
        $newsDTOs = [];
        $dto = new NewsDTO();
        $dto->title = $title;
        $dto->id = Uuid::v4()->toString();
        $dto->link = $link;
        $dto->created_at = new \DateTimeImmutable();
        $newsDTOs[] = $dto;

        $expectedContent = sprintf('<ul><li><a href="%s">%s</a></li></ul>', $link, $title);

        // Act
        $this->fileHelperMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($expectedContent),
                $this->matchesRegularExpression('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}\.html$/')
            );

        $result = $this->generator->generate($newsDTOs);

        // Assert
        $this->assertStringEndsWith('.html', $result);
        $this->assertTrue(Uuid::isValid(basename($result, '.html')));
    }
    public function testGenerateReportWithEmptyItem(): void
    {
        //Arrange
        $newsDTOs = [];
        $expectedContent = '<ul></ul>';

        // Act
        $this->fileHelperMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($expectedContent),
                $this->matchesRegularExpression('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}\.html$/')
            );

        $result = $this->generator->generate($newsDTOs);

        // Assert
        $this->assertStringEndsWith('.html', $result);
        $this->assertTrue(Uuid::isValid(basename($result, '.html')));
    }

    #[dataProvider('getDataProvider')]
    public function testGenerateReportWithMultipleItems(string $title, string $link): void
    {
        //Arrange
        $count = 10;
        $dto = new NewsDTO();
        $dto->title = $title;
        $dto->id = Uuid::v4()->toString();
        $dto->link = $link;
        $dto->created_at = new \DateTimeImmutable();

        for ($i = 0; $i < $count; $i++) {
            $newsDTOs[] = clone $dto;
        }
        $item = sprintf('<li><a href="%s">%s</a></li>', $link, $title);
        $expectedContent = '<ul>' . str_repeat($item, $count) . '</ul>';

        // Act
        $this->fileHelperMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($expectedContent),
                $this->matchesRegularExpression('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}\.html$/')
            );

        $result = $this->generator->generate($newsDTOs);

        // Assert
        $this->assertStringEndsWith('.html', $result);
        $this->assertTrue(Uuid::isValid(basename($result, '.html')));
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
        yield 'empty title' => [
            '',
            'https://example.com/empty'
        ];
        yield 'special characters title' => [
            'Title with "quotes" & special chars',
            'https://example.com/special/quotes'
        ];
    }
}