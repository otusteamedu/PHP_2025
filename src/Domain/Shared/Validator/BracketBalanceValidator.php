<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validator;

class BracketBalanceValidator
{
    public const string LEFT_BRACKET = '(';
    public const string RIGHT_BRACKET = ')';
    public const string VALID_BRACKET_PAIR = self::LEFT_BRACKET . self::RIGHT_BRACKET;

    private static int $counter = 0;

    /**
     * @note: рекурсивно уменьшает строку, убирая каждый раз валидные сочетания скобок "()";
     *        если строка на определённой итерации становится пустой, значит изначальная строка сбалансирована по скобкам,
     *        если строка на определённой итерации не пуста и при этом не содержит валидных сочетаний скобок, то изначальная строка не сбалансирована.
     */
    public static function isValid(string $string): bool
    {
        if (self::$counter === 0) {
            if ($string === '') {
                return false;
            }
            $leftBracketsCount = substr_count($string, self::LEFT_BRACKET);
            $rightBracketsCount = substr_count($string, self::RIGHT_BRACKET);
            if (
                $leftBracketsCount + $rightBracketsCount !== strlen($string)
                || $leftBracketsCount !== $rightBracketsCount
                || str_starts_with($string, self::RIGHT_BRACKET)
                || str_ends_with($string, self::LEFT_BRACKET)
            ) {
                return false;
            }
        }

        if ($string === '') {
            return true;
        }

        if (!str_contains($string, self::VALID_BRACKET_PAIR)) {
            return false;
        }

        $string = str_replace(self::VALID_BRACKET_PAIR, '', $string);

        self::$counter++;

        return self::isValid($string);
    }
}
