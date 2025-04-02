<?php
declare(strict_types=1);

namespace App;

use App\Infrastructure\Controller\DbDeleteAction;
use App\Infrastructure\Controller\DbInitAction;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use Exception;
use Throwable;

class App
{
    public static App $app;

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        self::$app = $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getConfigValue(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function run()
    {
        try {
            $result = $this->handleRequest(new Request());

            return json_encode($result->asJson(),JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            return json_encode(new Response('error', 400, null,$e->getMessage())->asJson(),JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        }
    }

    private function handleRequest(Request $request): Response
    {
        $controllerName = match ($request->getUrl()) {
            '/db/init' => DbInitAction::class,
            '/db/delete' => DbDeleteAction::class,
            default => throw new Exception("Invalid route."),
        };
        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not exist.");
        }

        return new $controllerName()($request);
    }

}