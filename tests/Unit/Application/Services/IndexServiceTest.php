<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\IndexService;
use App\Domain\Repositories\BookRepositoryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IndexServiceTest extends TestCase
{
    private IndexService $indexService;
    private BookRepositoryInterface $mockRepository;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(BookRepositoryInterface::class);
        $this->indexService = new IndexService($this->mockRepository);
    }

    /**
     * Тестирует, что createIndex удаляет существующий индекс
     */
    public function testCreateIndexDeletesExistingIndex(): void
    {
        // Arrange
        $this->mockRepository
            ->expects($this->once())
            ->method('indexExists')
            ->willReturn(true);

        $this->mockRepository
            ->expects($this->once())
            ->method('deleteIndex');

        $this->mockRepository
            ->expects($this->once())
            ->method('createIndex');

        // Act
        $this->indexService->createIndex();
    }

    /**
     * Тестирует, что createIndex не удаляет индекс когда он не существует
     */
    public function testCreateIndexDoesNotDeleteWhenIndexDoesNotExist(): void
    {
        // Arrange
        $this->mockRepository
            ->expects($this->once())
            ->method('indexExists')
            ->willReturn(false);

        $this->mockRepository
            ->expects($this->never())
            ->method('deleteIndex');

        $this->mockRepository
            ->expects($this->once())
            ->method('createIndex');

        // Act
        $this->indexService->createIndex();
    }

    /**
     * Тестирует, что indexBooksFromFile выбрасывает исключение когда файл не найден
     */
    public function testIndexBooksFromFileThrowsExceptionWhenFileNotFound(): void
    {
        // Arrange
        $nonExistentFile = '/path/to/nonexistent/file.json';

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Файл не найден: {$nonExistentFile}");

        $this->indexService->indexBooksFromFile($nonExistentFile);
    }

    /**
     * Тестирует успешную индексацию книг из файла
     */
    public function testIndexBooksFromFileSuccessfullyIndexesBooks(): void
    {
        // Arrange
        $tempFile = $this->createTempFileWithBooks();

        $this->mockRepository
            ->expects($this->once())
            ->method('bulkIndex')
            ->with($this->callback(function (array $books) {
                return count($books) === 2 &&
                       $books[0]->getTitle() === 'Book 1' &&
                       $books[1]->getTitle() === 'Book 2';
            }));

        // Act
        $result = $this->indexService->indexBooksFromFile($tempFile);

        // Assert
        $this->assertEquals(2, $result);

        // Cleanup
        unlink($tempFile);
    }

    /**
     * Тестирует обработку пустого файла при индексации
     */
    public function testIndexBooksFromFileHandlesEmptyFile(): void
    {
        // Arrange
        $tempFile = $this->createTempFileWithContent('');

        $this->mockRepository
            ->expects($this->once())
            ->method('bulkIndex')
            ->with([]);

        // Act
        $result = $this->indexService->indexBooksFromFile($tempFile);

        // Assert
        $this->assertEquals(0, $result);

        // Cleanup
        unlink($tempFile);
    }

    /**
     * Тестирует пропуск некорректных JSON строк при индексации
     */
    public function testIndexBooksFromFileSkipsInvalidJsonLines(): void
    {
        // Arrange
        $content = "{\"title\": \"Book 1\", \"id\": \"1\"}\n";
        $content .= "invalid json line\n";
        $content .= "{\"title\": \"Book 2\", \"id\": \"2\"}\n";
        
        $tempFile = $this->createTempFileWithContent($content);

        $this->mockRepository
            ->expects($this->once())
            ->method('bulkIndex')
            ->with($this->callback(function (array $books) {
                return count($books) === 2 &&
                       $books[0]->getTitle() === 'Book 1' &&
                       $books[1]->getTitle() === 'Book 2';
            }));

        // Act
        $result = $this->indexService->indexBooksFromFile($tempFile);

        // Assert
        $this->assertEquals(2, $result);

        // Cleanup
        unlink($tempFile);
    }

    /**
     * Тестирует пропуск операций create при индексации
     */
    public function testIndexBooksFromFileSkipsCreateOperations(): void
    {
        // Arrange
        $content = "{\"create\": {\"_index\": \"books\"}}\n";
        $content .= "{\"title\": \"Book 1\", \"id\": \"1\"}\n";
        
        $tempFile = $this->createTempFileWithContent($content);

        $this->mockRepository
            ->expects($this->once())
            ->method('bulkIndex')
            ->with($this->callback(function (array $books) {
                return count($books) === 1 &&
                       $books[0]->getTitle() === 'Book 1';
            }));

        // Act
        $result = $this->indexService->indexBooksFromFile($tempFile);

        // Assert
        $this->assertEquals(1, $result);

        // Cleanup
        unlink($tempFile);
    }

    /**
     * Создает временный файл с тестовыми книгами
     */
    private function createTempFileWithBooks(): string
    {
        $content = "{\"title\": \"Book 1\", \"id\": \"1\", \"category\": \"Fiction\", \"price\": 1000, \"stock\": []}\n";
        $content .= "{\"title\": \"Book 2\", \"id\": \"2\", \"category\": \"Non-Fiction\", \"price\": 1500, \"stock\": []}\n";
        
        return $this->createTempFileWithContent($content);
    }

    /**
     * Создает временный файл с указанным содержимым
     */
    private function createTempFileWithContent(string $content): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'books_test_');
        file_put_contents($tempFile, $content);

        return $tempFile;
    }
}
