<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\ValueObject;

use MkdBot\Domain\ValueObject\RagSearchSource;
use PHPUnit\Framework\TestCase;

class RagSearchSourceTest extends TestCase
{
    public function testConstructorWithAllFields(): void
    {
        $source = new RagSearchSource('faq.md', 'file-abc', 0.95, 'Фрагмент текста');

        $this->assertSame('faq.md', $source->getFilename());
        $this->assertSame('file-abc', $source->getFileId());
        $this->assertSame(0.95, $source->getScore());
        $this->assertSame('Фрагмент текста', $source->getText());
    }

    public function testConstructorWithDefaults(): void
    {
        $source = new RagSearchSource('contacts.json');

        $this->assertSame('contacts.json', $source->getFilename());
        $this->assertNull($source->getFileId());
        $this->assertNull($source->getScore());
        $this->assertSame('', $source->getText());
    }

    public function testFromArrayWithFullData(): void
    {
        $data = [
            'filename' => 'faq.md',
            'file_id' => 'file-xyz',
            'score' => 0.87,
            'text' => 'Текст фрагмента',
        ];

        $source = RagSearchSource::fromArray($data);

        $this->assertSame('faq.md', $source->getFilename());
        $this->assertSame('file-xyz', $source->getFileId());
        $this->assertSame(0.87, $source->getScore());
        $this->assertSame('Текст фрагмента', $source->getText());
    }

    public function testFromArrayWithMinimalData(): void
    {
        $source = RagSearchSource::fromArray([]);

        $this->assertSame('Неизвестный файл', $source->getFilename());
        $this->assertNull($source->getFileId());
        $this->assertNull($source->getScore());
        $this->assertSame('', $source->getText());
    }

    public function testFromArrayWithCitationData(): void
    {
        $data = [
            'filename' => 'jk.pdf',
            'file_id' => 'file-citation-1',
        ];

        $source = RagSearchSource::fromArray($data);

        $this->assertSame('jk.pdf', $source->getFilename());
        $this->assertSame('file-citation-1', $source->getFileId());
        $this->assertNull($source->getScore());
        $this->assertSame('', $source->getText());
    }
}
