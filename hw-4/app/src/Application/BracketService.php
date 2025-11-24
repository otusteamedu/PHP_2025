<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\BracketValidator;

final readonly class BracketService
{
    public function __construct(private BracketValidator $validator)
    {
    }

    public function check(string $bracketString): bool
    {
        return $this->validator->checkParenthesesBalance($bracketString);
    }
}
