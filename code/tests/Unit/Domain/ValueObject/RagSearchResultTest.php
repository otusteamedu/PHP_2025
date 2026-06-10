<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\ValueObject;

use MkdBot\Domain\ValueObject\RagSearchResult;
use MkdBot\Domain\ValueObject\RagSearchSource;
use PHPUnit\Framework\TestCase;

class RagSearchResultTest extends TestCase
{
    public function testSuccessFactoryCreatesSuccessfulResult(): void
    {
        $result = RagSearchResult::success('Ответ на вопрос');

        $this->assertTrue($result->isSuccess());
        $this->assertSame('Ответ на вопрос', $result->getAnswer());
        $this->assertSame([], $result->getSources());
        $this->assertSame(0, $result->getErrorCode());
        $this->assertSame('', $result->getErrorMessage());
    }

    public function testSuccessFactoryWithSources(): void
    {
        $sources = [
            new RagSearchSource('faq.md', 'file-123', 0.95, 'Фрагмент текста'),
        ];

        $result = RagSearchResult::success('Ответ', $sources);

        $this->assertTrue($result->isSuccess());
        $this->assertCount(1, $result->getSources());
        $this->assertSame('faq.md', $result->getSources()[0]->getFilename());
    }

    public function testErrorFactoryCreatesErrorResult(): void
    {
        $result = RagSearchResult::error(502, 'Сервис недоступен');

        $this->assertFalse($result->isSuccess());
        $this->assertSame('', $result->getAnswer());
        $this->assertSame([], $result->getSources());
        $this->assertSame(502, $result->getErrorCode());
        $this->assertSame('Сервис недоступен', $result->getErrorMessage());
    }

    public function testDefaultConstructorCreatesSuccessfulResult(): void
    {
        $result = new RagSearchResult();

        $this->assertTrue($result->isSuccess());
        $this->assertSame('', $result->getAnswer());
        $this->assertSame(0, $result->getErrorCode());
    }
}
