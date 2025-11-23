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

    public function handle(): HttpResponse
    {
        $input = $_POST['string'] ?? '';

        if ($input === '') {
            return new HttpResponse(400, "Пустая строка");
        }

        if (!$this->containsOnlyParentheses($input)) {
            return new HttpResponse(400, "Нужна строка только с круглыми скобками");
        }

        if ($this->bracketService->check($input)) {
            return new HttpResponse(200, "Скобки сбалансированы");
        }

        return new HttpResponse(400, "Скобки не сбалансированы");
    }

    private function containsOnlyParentheses(string $postString): bool
    {
        return preg_match('/^[()]+$/', $postString) === 1;
    }
}
