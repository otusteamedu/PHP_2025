<?php

declare(strict_types=1);

namespace App\Validator;

class ParenthesisStringValidator
{
    public const string LEFT_PARENTHESIS = '(';
    public const string RIGHT_PARENTHESIS = ')';
    public const string VALID_PARENTHESIS_PAIR = self::LEFT_PARENTHESIS . self::RIGHT_PARENTHESIS;

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
            $leftParenthesisCount = substr_count($string, self::LEFT_PARENTHESIS);
            $rightParenthesisCount = substr_count($string, self::RIGHT_PARENTHESIS);
            if (
                $leftParenthesisCount + $rightParenthesisCount !== strlen($string)
                || $leftParenthesisCount !== $rightParenthesisCount
                || str_starts_with($string, self::RIGHT_PARENTHESIS)
                || str_ends_with($string, self::LEFT_PARENTHESIS)
            ) {
                return false;
            }
        }

        if ($string === '') {
            return true;
        }

        if (!str_contains($string, self::VALID_PARENTHESIS_PAIR)) {
            return false;
        }

        $string = str_replace(self::VALID_PARENTHESIS_PAIR, '', $string);

        self::$counter++;

        return self::isValid($string);
    }
}
