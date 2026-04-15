<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Kernel;

use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\Html;
use Otus\Queue\Infrastructure\Http\Response\Json;
use Otus\Queue\Infrastructure\Kernel\Http\Kernel;
use PHPUnit\Framework\TestCase;

final class HttpKernelTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Container::class);
        $instance = $reflection->getProperty('instance');
        $instance->setValue(null, null);
    }

    public function testRunReturnsControllerResponseForMatchedRoute(): void
    {
        $kernel = new Kernel([
            'routes' => [
                [Method::GET, '/ok/{id}', HttpKernelController::class],
            ],
        ]);

        $run = new \ReflectionMethod($kernel, 'run');

        $response = $run->invoke($kernel, $this->request(Method::GET, '/ok/15'));

        self::assertInstanceOf(Json::class, $response);
        self::assertSame(200, $response->getStatusCode());
    }

    public function testRunReturnsNotFoundResponseWhenRouteMissing(): void
    {
        $kernel = new Kernel([
            'routes' => [],
        ]);

        $run = new \ReflectionMethod($kernel, 'run');

        $response = $run->invoke($kernel, $this->request(Method::GET, '/missing'));

        self::assertInstanceOf(Html::class, $response);
        self::assertSame(404, $response->getStatusCode());
    }

    public function testHandleWrapsThrowableInto500Response(): void
    {
        $kernel = new Kernel([
            'routes' => [
                [Method::GET, '/fail', HttpKernelFailController::class],
            ],
        ]);

        ob_start();
        $kernel->handle($this->request(Method::GET, '/fail', ['Accept' => 'application/json']));
        $output = ob_get_clean();

        self::assertStringContainsString('failure', (string) $output);
    }

    private function request(Method $method, string $uri, array $headers = [], array $body = []): Request
    {
        return new Request(
            method: $method,
            uri: $uri,
            headers: Collection::make($headers),
            queryParams: Collection::make(),
            body: Collection::make($body),
        );
    }
}

final class HttpKernelController
{
    public function __invoke(Request $request, string $id): Json
    {
        return Json::create(['id' => (int) $id]);
    }
}

final class HttpKernelFailController
{
    public function __invoke(): Json
    {
        throw new \RuntimeException('failure');
    }
}
