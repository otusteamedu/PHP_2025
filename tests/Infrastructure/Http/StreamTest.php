<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Http;

use App\Infrastructure\Http\Stream;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{

    private const DEFAULT_CONTENT = 'default content';
    private const NEW_CONTENT = 'new content';

    public function testToStringAndGetContents(): void
    {
        $stream = new Stream(self::DEFAULT_CONTENT);
        $this->assertSame(self::DEFAULT_CONTENT, (string)$stream);
        $this->assertSame(self::DEFAULT_CONTENT, $stream->getContents());
    }

    public function testWithContentsIsImmutable(): void
    {
        $streamDefault = new Stream(self::DEFAULT_CONTENT);
        $streamNew = $streamDefault->withContents(self::NEW_CONTENT);

        $this->assertNotSame($streamDefault, $streamNew);
        $this->assertSame(self::DEFAULT_CONTENT, (string)$streamDefault);
        $this->assertSame(self::NEW_CONTENT, (string)$streamNew);
    }
}
