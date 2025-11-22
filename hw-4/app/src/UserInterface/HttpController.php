<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\BracketService;

readonly class HttpController
{
    public function __construct(
        private BracketService $bracketService,
    ) {
    }

    public function handle(): void
    {
        $input = $_POST['string'] ?? '';

        if ($input === '') {
            http_response_code(400);
            echo "Пустая строка";
            return;
        }

        if (!$this->containsOnlyParentheses($input)) {
            http_response_code(400);
            echo "Нужна строка только с круглыми скобками";
            return;
        }

        if ($this->bracketService->check($input)) {
            http_response_code(200);
            echo "Скобки сбалансированы";
        } else {
            http_response_code(400);
            echo "Скобки не сбалансированы";
        }
    }

    private function containsOnlyParentheses(string $postString): bool
    {
        return preg_match('/^[()]+$/', $postString) === 1;
    }
}
