<?php declare(strict_types=1);

namespace classes;

class StringValidator
{
    public function isStringNotExists(string $str): bool
    {
        return (!isset($str) || $str == '');
    }

    public function checkBrackets(string $str): bool
    {
        // Преобразуем строку в массив символов
        $chars = str_split($str);
        $open = 0;
        $close = 0;

        foreach ($chars as $char) {
            switch ($char) {
                case '(':
                    $open++;
                    break;
                case ')':
                    $close++;
                    break;
            }
        }

        $openAndCloseBracketsEqualAmount = $open == $close;
        $correctPairsOfBrackets = preg_match('/(\([^()]*\)|\{[^{}]*\}|<[^<>]*>|\[[^\[\]]*\]|\(.*\))/', $str) === 1;

        return ($openAndCloseBracketsEqualAmount && $correctPairsOfBrackets);
    }
}