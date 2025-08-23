<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Console;

use App\Infrastructure\Console\TableRenderer;
use App\Domain\Models\Book;
use PHPUnit\Framework\TestCase;

final class TableRendererTest extends TestCase
{
    private TableRenderer $renderer;

    /**
     * Настройка тестового окружения
     */
    protected function setUp(): void
    {
        $this->renderer = new TableRenderer();
    }

    /**
     * Тестирует создание рендерера
     */
    public function testRendererCreation(): void
    {
        $this->assertInstanceOf(TableRenderer::class, $this->renderer);
    }

    /**
     * Тестирует рендеринг пустых результатов поиска
     */
    public function testRenderEmptySearchResults(): void
    {
        $books = [];
        $output = $this->renderer->renderSearchResults($books, 0, 0.0);
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Поиск не дал результатов', $output);
    }

    /**
     * Тестирует рендеринг результатов поиска с книгами
     */
    public function testRenderSearchResultsWithBooks(): void
    {
        $books = [
            new Book('1', 'Test Book 1', 'Test Category', 1000, [['store' => 'Store 1', 'stock' => 5]]),
            new Book('2', 'Test Book 2', 'Test Category', 2000, [['store' => 'Store 2', 'stock' => 0]])
        ];
        
        $output = $this->renderer->renderSearchResults($books, 2, 10.5);
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Найдено книг: 2', $output);
        $this->assertStringContainsString('Test Book 1', $output);
        $this->assertStringContainsString('Test Book 2', $output);
        $this->assertStringContainsString('Test Category', $output);
    }

    /**
     * Тестирует рендеринг сообщения об ошибке
     */
    public function testRenderError(): void
    {
        $message = 'Test error message';
        $output = $this->renderer->renderError($message);
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Ошибка: Test error message', $output);
    }

    /**
     * Тестирует рендеринг информационного сообщения
     */
    public function testRenderInfo(): void
    {
        $message = 'Test info message';
        $output = $this->renderer->renderInfo($message);
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Test info message', $output);
    }
}
