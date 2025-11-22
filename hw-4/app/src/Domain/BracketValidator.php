<?php

declare(strict_types=1);

namespace App\Domain;

final readonly class BracketValidator
{
    public function checkParenthesesBalance(string $bracketString): bool
    {
        $balance = 0;

        for ($i = 0; $i < strlen($bracketString); $i++) {
            $char = $bracketString[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    return false;
                }
            }
        }

        return $balance === 0;
    }
}
