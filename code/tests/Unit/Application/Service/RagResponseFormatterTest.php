<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\Service;

use MkdBot\Application\Service\RagResponseFormatter;
use MkdBot\Domain\ValueObject\RagSearchResult;
use MkdBot\Domain\ValueObject\RagSearchSource;
use PHPUnit\Framework\TestCase;

class RagResponseFormatterTest extends TestCase
{
    private RagResponseFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new RagResponseFormatter();
    }

    // --- Успешные результаты ---

    public function testFormatSuccessWithAnswer(): void
    {
        $result = RagSearchResult::success('Оплата ЖКХ производится через банк');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('🤖 Оплата ЖКХ производится через банк', $text);
    }

    public function testFormatSuccessWithSources(): void
    {
        $sources = [
            new RagSearchSource('faq.md', 'file-1', 0.95, 'Фрагмент'),
            new RagSearchSource('contacts.json', 'file-2', null, ''),
        ];

        $result = RagSearchResult::success('Ответ', $sources);

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('📚 Источники:', $text);
        $this->assertStringContainsString('📄 faq.md', $text);
        $this->assertStringContainsString('соответствие: 95%', $text);
        $this->assertStringContainsString('📄 contacts.json', $text);
    }

    public function testFormatSuccessWithSourceWithoutScore(): void
    {
        $sources = [
            new RagSearchSource('jk.pdf', 'file-3', null, ''),
        ];

        $result = RagSearchResult::success('Ответ', $sources);

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('📄 jk.pdf', $text);
        $this->assertStringNotContainsString('соответствие', $text);
    }

    public function testFormatDeduplicatesSourcesByFilename(): void
    {
        $sources = [
            new RagSearchSource('contacts.json', 'file-1', 1.56, 'Фрагмент 1'),
            new RagSearchSource('jk.pdf', 'file-2', 1.35, 'Фрагмент 2'),
            new RagSearchSource('jk.pdf', 'file-3', 1.37, 'Фрагмент 3'),
            new RagSearchSource('jk.pdf', 'file-4', 1.33, 'Фрагмент 4'),
            new RagSearchSource('contacts.json', 'file-5', null, ''),
        ];

        $result = RagSearchResult::success('Ответ', $sources);

        $text = $this->formatter->format($result);

        $this->assertEquals(1, substr_count($text, '📄 contacts.json'));
        $this->assertEquals(1, substr_count($text, '📄 jk.pdf'));

        $this->assertStringContainsString('📄 jk.pdf (соответствие: 137%)', $text);

        $this->assertStringContainsString('📄 contacts.json (соответствие: 156%)', $text);
    }

    public function testFormatSuccessWithEmptyAnswer(): void
    {
        $result = RagSearchResult::success('');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('не удалось найти ответ', $text);
    }

    public function testFormatTruncatesLongAnswer(): void
    {
        $longAnswer = str_repeat('А', 5000);
        $result = RagSearchResult::success($longAnswer);

        $text = $this->formatter->format($result);

        $this->assertLessThanOrEqual(4000, mb_strlen($text));
        $this->assertStringContainsString('текст сокращён', $text);
    }

    // --- Ошибки ---

    public function testFormatError401(): void
    {
        $result = RagSearchResult::error(401, 'Неверный API-ключ');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('Сервис временно недоступен', $text);
    }

    public function testFormatError400(): void
    {
        $result = RagSearchResult::error(400, 'Вопрос обязателен');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('Не удалось обработать вопрос', $text);
    }

    public function testFormatError500(): void
    {
        $result = RagSearchResult::error(500, 'Внутренняя ошибка');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('ошибка при поиске ответа', $text);
    }

    public function testFormatError502(): void
    {
        $result = RagSearchResult::error(502, 'Сервис недоступен');

        $text = $this->formatter->format($result);

        $this->assertStringContainsString('ошибка при поиске ответа', $text);
    }
}
