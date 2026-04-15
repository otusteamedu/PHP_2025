<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http {
    function getallheaders(): array
    {
        return ['Accept' => 'application/json'];
    }
}

namespace Otus\Queue\Tests\Infrastructure\Http {
    use Otus\Queue\Infrastructure\Component\Collection;
    use Otus\Queue\Infrastructure\Http\Method;
    use Otus\Queue\Infrastructure\Http\Request;
    use PHPUnit\Framework\TestCase;

    final class RequestTest extends TestCase
    {
        protected function tearDown(): void
        {
            $_SERVER = [];
            $_GET = [];
            $_POST = [];
        }

        public function testFromGlobalsBuildsRequestObject(): void
        {
            $_SERVER['REQUEST_METHOD'] = 'post';
            $_SERVER['REQUEST_URI'] = '/chat/store?x=1';
            $_GET = ['x' => '1'];
            $_POST = ['author' => 'alex', 'text' => 'hello'];

            $request = Request::fromGlobals();

            self::assertSame(Method::POST, $request->method);
            self::assertSame('/chat/store', $request->uri);
            self::assertInstanceOf(Collection::class, $request->queryParams);
            self::assertSame(['x' => '1'], $request->queryParams->all());
            self::assertSame(['author' => 'alex', 'text' => 'hello'], $request->body->all());
            self::assertSame('application/json', $request->headers->get('Accept'));
        }
    }
}
