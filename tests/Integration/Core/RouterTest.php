<?php

namespace Tests\Integration\Core;

use App\Core\Container;
use App\Core\Router;
use App\Core\Exceptions\NotFoundException;
use Infrastructure\Adapter\FastFoodItemInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $container = new Container();

        // Регистрируем необходимые зависимости
        $container->bind(
            FastFoodItemInterface::class,
            fn() => $this->createMock(FastFoodItemInterface::class)
        );

        $this->router = new Router($container);
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testRouteResolution()
    {
        $_SERVER['REQUEST_URI'] = '/orders/list';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $route = $this->router->getRoute();
        $this->assertArrayHasKey('controller', $route);
        $this->assertArrayHasKey('method', $route);
    }

    /**
     * @throws ReflectionException
     */
    public function testNonexistentRoute()
    {
        $_SERVER['REQUEST_URI'] = '/nonexistent';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->expectException(NotFoundException::class);
        $this->router->getRoute();
    }
}