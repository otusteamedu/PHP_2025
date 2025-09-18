<?php
declare(strict_types=1);

namespace App\Application;

use App\Http\Response;
use App\Service\LastCheckService;
use App\Service\ParenthesesValidator;
use App\View\Renderer;

final readonly class App
{
    public function __construct(
        private ParenthesesValidator $validator,
        private Renderer $renderer,
        private LastCheckService $lastCheckService
    ) {}

    public function run(): Response
    {
        if ($this->isPostRequest()) {
            return $this->handlePostRequest();
        }

        return $this->handleGetRequest();
    }

    private function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function getPostParam(string $key): mixed
    {
        return $_POST[$key] ?? null;
    }

    private function handlePostRequest(): Response
    {
        $this->lastCheckService->setCurrentTime();
        $input = (string) $this->getPostParam('string');
        $isBalanced = $this->validator->isBalanced($input);

        $status = $isBalanced ? 200 : 400;
        $body = $isBalanced
            ? 'Строка корректна: всё хорошо'
            : 'Строка некорректна: всё плохо';

        return new Response($status, $body);
    }

    private function handleGetRequest(): Response
    {
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        $lastCheckStr = $this->lastCheckService->get();
        $body = $this->renderer->renderForm($serverAddr, $lastCheckStr);

        return new Response(200, $body);
    }
}
