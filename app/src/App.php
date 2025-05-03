<?php
declare(strict_types=1);

namespace App;

use App\Food\Infrastructure\Controller\AddBurgerAction;
use App\Food\Infrastructure\Controller\PlaceOrderAction;
use App\Shared\Infrastructure\Controller\HealthCheckAction;
use App\Shared\Infrastructure\Controller\MigrationAction;
use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Http\Response;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Throwable;

class App
{
    public static App $app;
    private array $config {
        get {
            return $this->config;
        }
    }

    public function __construct(array $config, private readonly Container $container)
    {
        $this->config = $config;
        self::$app = $this;
    }

    public function getConfigValue(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function run()
    {
        try {
            $result = $this->handleRequest(new Request());
            $response = $result;
            return json_encode($response);
        } catch (Throwable $e) {
            $response = new Response('error', 400, null, $e->getMessage());
            return json_encode($response->asJson());
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function handleRequest(Request $request): Response
    {
        $controllerName = match ($request->getUrl()) {
            '/health-check' => HealthCheckAction::class,
            '/migrate' => MigrationAction::class,
            '/make-burger' => AddBurgerAction::class,
            '/place-order' => PlaceOrderAction::class,
            '/order/add-burger' => AddBurgerAction::class,



            default => throw new Exception('invalid route'),
        };
        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not exist.");
        }

        $controller = $this->container->get($controllerName);

        return $controller($request);
    }

}