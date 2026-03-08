<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Http\Exception;

use Exception;
use Otus\Queue\Infrastructure\Http\Exception\ForbiddenHttpException;
use Otus\Queue\Infrastructure\Http\Exception\NotFoundHttpException;
use Otus\Queue\Infrastructure\Http\Exception\ServerInternalErrorHttpException;
use PHPUnit\Framework\TestCase;

final class HttpExceptionTest extends TestCase
{
    public function testForbiddenExceptionHasExpectedMessageAndCode(): void
    {
        $exception = new ForbiddenHttpException();

        self::assertSame('Forbidden.', $exception->getMessage());
        self::assertSame(403, $exception->getStatusCode());
    }

    public function testNotFoundExceptionHasExpectedMessageAndCode(): void
    {
        $exception = new NotFoundHttpException();

        self::assertSame('Not Found.', $exception->getMessage());
        self::assertSame(404, $exception->getStatusCode());
    }

    public function testServerInternalExceptionUsesThrowableMessage(): void
    {
        $exception = new ServerInternalErrorHttpException(new Exception('boom'));

        self::assertSame('boom', $exception->getMessage());
        self::assertSame(500, $exception->getStatusCode());
    }
}
