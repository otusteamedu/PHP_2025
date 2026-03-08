<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Http;

use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Infrastructure\Http\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testMatchReturnsPathParameters(): void
    {
        $route = new Route(Method::GET, '/chat/{room}/{id}', 'Controller');

        self::assertSame(
            ['room' => 'general', 'id' => '42'],
            $route->match('/chat/general/42')
        );
    }

    public function testMatchReturnsNullForNonMatchingUri(): void
    {
        $route = new Route(Method::GET, '/chat/{id}', 'Controller');

        self::assertNull($route->match('/posts/1'));
    }
}
