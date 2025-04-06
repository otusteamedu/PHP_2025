<?php
declare(strict_types=1);


namespace App\Infrastructure\Http;

class Request
{
    private array $params = [];
    private string $route = '';

    public function __construct()
    {
        $this->parseParams();
        $this->parseRoute();
    }

    private function parseParams(): void
    {
        foreach ($_SERVER['argv'] as $value) {
            if (preg_match('/^--[a-zA-Z]+=.+$/', $value, $matches)) {
                list($key, $value) = explode('=', substr($matches[0], strlen('--')));
                $this->params[$key] = $value;
            }
        }
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key): ?string
    {
        return $this->params[$key] ?? null;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    private function parseRoute(): void
    {
        $this->route = $_SERVER['argv'][1] ?? '';
    }

}