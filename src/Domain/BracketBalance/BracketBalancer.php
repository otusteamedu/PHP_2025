<?php

declare(strict_types=1);

namespace App\Domain\BracketBalance;

use App\Domain\Shared\Validator\BracketBalanceValidator;

class BracketBalancer
{
    public function __construct(
        private readonly BracketBalanceValidator $bracketBalanceValidator,
    ) {
    }

    public function validateBracketString(string $str): bool
    {
        return $this->bracketBalanceValidator->isValid($str);
    }
}
