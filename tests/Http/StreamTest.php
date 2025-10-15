<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Http\Stream;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{
    public function testToStringAndGetContents(): void
    {
        $s = new Stream('abc');
        $this->assertSame('abc', (string)$s);
        $this->assertSame('abc', $s->getContents());
    }

    public function testWithContentsIsImmutable(): void
    {
        $s1 = new Stream('a');
        $s2 = $s1->withContents('b');

        $this->assertNotSame($s1, $s2);
        $this->assertSame('a', (string)$s1);
        $this->assertSame('b', (string)$s2);
    }
}
