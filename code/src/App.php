<?php

declare(strict_types=1);

namespace src;

use src\Controller\BracketsController;
use src\Http\Response;
use src\Service\BracketValidator;
use Throwable;

final class App
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/settings.php';
    }

    public function run(): void
    {
        try {
            $this->initSession();

            $controller = new BracketsController(new BracketValidator());
            $response = $controller->handle();

            $this->emit($response);

        } catch (Throwable $e) {
            $this->emit(new Response([
                'error' => true,
                'message' => $e->getMessage()
            ], (int)$e->getCode() ?: 500));
        }
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.save_handler', $this->config['session']['handler']);
            ini_set('session.save_path', $this->config['session']['path']);
            session_start();
        }
    }

    private function emit(Response $response): void
    {
        if (PHP_SAPI !== 'cli' && !headers_sent()) {
            http_response_code($response->statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response->data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
