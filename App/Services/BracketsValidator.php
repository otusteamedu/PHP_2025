<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\EmptyStringException;
use App\Exceptions\InvalidBracketsException;
use App\Interfaces\BracketsValidatorInterface;

class BracketsValidator implements BracketsValidatorInterface
{
    private const ALLOWED_CHARS = ['(', ')'];

    public function validate(string $input): bool
    {
        $this->validateNotEmpty($input);
        $this->validateChars($input);

        return $this->isBalanced($input);
    }

    private function validateNotEmpty(string $input): void
    {
        if (empty($input)) {
            throw new EmptyStringException();
        }
    }

    private function validateChars(string $input): void
    {
        $invalidChars = array_diff(
            str_split($input),
            self::ALLOWED_CHARS
        );

        if (!empty($invalidChars)) {
            throw new InvalidBracketsException(
                sprintf('String contains invalid characters: %s', implode(', ', $invalidChars))
            );
        }
    }

    private function isBalanced(string $input): bool
    {
        $balance = 0;

        for($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
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