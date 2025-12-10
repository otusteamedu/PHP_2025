<?php

declare(strict_types=1);

namespace src;

use Exception;
use InvalidArgumentException;
use src\Controller\BracketsController;
use src\Http\Response;
use src\Service\BracketValidator;

readonly class App
{
    public function __construct(
        readonly private array $config
    ) {}

    public function run(): Response
    {
        $this->initSession();

        $validator = new BracketValidator();
        $controller = new BracketsController($validator);

        return $controller->handle();
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.save_handler', $this->config['session']['handler']);
            ini_set('session.save_path', $this->config['session']['path']);
            session_start();
        }
    }
}
