<?php
declare(strict_types=1);

namespace App;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Controller\HealthCheckAction;
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

    public function __construct(array $config)
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

    private function handleRequest(Request $request): Response
    {
        $controllerName = match ($request->getUrl()) {
            '/health-check' => HealthCheckAction::class,
            default => throw new Exception('invalid route'),
        };
        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not exist.");
        }

        return new $controllerName()($request);
    }

}