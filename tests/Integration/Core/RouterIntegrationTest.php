<?php

declare(strict_types=1);

namespace Tests\Integration\Core;

use App\Core\Container;
use App\Core\Exceptions\NotFoundException;
use App\Core\Router;
use Infrastructure\Adapter\FastFoodItemInterface;
use Infrastructure\Adapter\PizzaAdapter;
use Infrastructure\Adapter\PizzaInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class RouterIntegrationTest extends TestCase
{
    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testRouteResolution()
    {
        $container = new Container();

        $container->bind(
            FastFoodItemInterface::class,
            fn($c) => new PizzaAdapter(
                $c->make(PizzaInterface::class)
            )
        );
        $container->bind(
            PizzaInterface::class,
            fn() => new \Infrastructure\Adapter\Pizza()
        );

        $router = new Router($container);

        $_SERVER['REQUEST_URI'] = '/orders/list';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $route = $router->getRoute();
        $this->assertArrayHasKey('controller', $route);
    }
}