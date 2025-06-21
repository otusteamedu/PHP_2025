<?php

declare(strict_types=1);

namespace App\Tests\News\Infrastructure\Service;

use App\News\Application\DTO\NewsDTO;
use App\News\Application\Service\NewsReportGeneratorInterface;
use App\Shared\Infrastructure\Kernel;
use App\Tests\Tools\DITools;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class HtmlNewsReportGeneratorGeneratorTest extends KernelTestCase
{
    use DITools;

    private NewsReportGeneratorInterface $htmlNewsReportGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        // Явно запускаем ядро перед тестами
        self::bootKernel();

        $this->htmlNewsReportGenerator = $this->getService(NewsReportGeneratorInterface::class);

    }

    public function test_news_generated_successfully(): void
    {
        // Arrange
        $dto = new NewsDTO();
        $dto->id = Uuid::v4()->toRfc4122();
        $dto->title = 'Hello World';
        $dto->link = 'https://google.com';
        $dto->created_at = new \DateTimeImmutable();

        // Act
        $result = $this->htmlNewsReportGenerator->generate([$dto]);

        // Assert
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\.html$/i',
            basename($result),
            'Имя файла не соответствует ожидаемому формату UUIDv4.html'
        );
    }

    // Добавляем метод для определения класса ядра
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}