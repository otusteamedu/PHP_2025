<?php
declare(strict_types=1);

namespace App;

use App\Infrastructure\Console\Request;
use App\Infrastructure\Console\Response;
use App\Infrastructure\Controller\AddAction;
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
            if ($result->isSuccess()) {
                $response = $result->data ?? $result->result;
            } else {
                $response = $result->message;
            }

            return $response;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    private function handleRequest(Request $request): Response
    {
        $controllerName = match ($request->getRoute()) {
            'health-check' => HealthCheckAction::class,
            'add-event' => AddAction::class,
            default => throw new Exception("Invalid route."),
        };
        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not exist.");
        }

        return new $controllerName()($request);
    }

}