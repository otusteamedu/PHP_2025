<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Http;

use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Route;
use Otus\Queue\Infrastructure\Http\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testMethodHelpersRegisterRoutesForDifferentHttpMethods(): void
    {
        $router = new Router();

        self::assertSame($router, $router->post('/posts', 'PostController', 'store'));
        self::assertSame($router, $router->put('/posts/{id}', 'PostController', 'update'));
        self::assertSame($router, $router->delete('/posts/{id}', 'PostController', 'destroy'));

        $post = $router->resolve($this->request(Method::POST, '/posts'));
        $put = $router->resolve($this->request(Method::PUT, '/posts/7'));
        $delete = $router->resolve($this->request(Method::DELETE, '/posts/7'));

        self::assertSame('store', $post[0]->action);
        self::assertSame(['id' => '7'], $put[1]);
        self::assertSame('destroy', $delete[0]->action);
    }

    public function testResolveReturnsMatchingRouteAndParams(): void
    {
        $router = new Router();
        self::assertSame($router, $router->get('/chat/{id}', 'ChatController', 'show'));

        $resolved = $router->resolve($this->request(Method::GET, '/chat/12'));

        self::assertIsArray($resolved);
        self::assertInstanceOf(Route::class, $resolved[0]);
        self::assertSame('ChatController', $resolved[0]->controller);
        self::assertSame(['id' => '12'], $resolved[1]);
    }

    public function testResolveReturnsFallbackWhenRouteNotFound(): void
    {
        $router = new Router();
        self::assertSame($router, $router->fallback('FallbackController'));

        $resolved = $router->resolve($this->request(Method::GET, '/missing'));

        self::assertIsArray($resolved);
        self::assertSame('FallbackController', $resolved[0]->controller);
        self::assertSame([], $resolved[1]);
    }

    public function testResolveReturnsNullWhenNoRouteAndNoFallback(): void
    {
        $router = new Router();

        self::assertNull($router->resolve($this->request(Method::GET, '/missing')));
    }

    private function request(Method $method, string $uri): Request
    {
        return new Request(
            method: $method,
            uri: $uri,
            headers: Collection::make(),
            queryParams: Collection::make(),
            body: Collection::make(),
        );
    }
}
