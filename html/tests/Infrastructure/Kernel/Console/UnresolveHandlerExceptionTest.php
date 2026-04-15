<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Kernel\Console;

use Otus\Queue\Infrastructure\Kernel\Console\UnresolveHandlerException;
use PHPUnit\Framework\TestCase;

final class UnresolveHandlerExceptionTest extends TestCase
{
    public function testMessageContainsCommandName(): void
    {
        $exception = new UnresolveHandlerException('chat:publish');

        self::assertSame('Unresolvable handler `chat:publish`', $exception->getMessage());
    }
}
