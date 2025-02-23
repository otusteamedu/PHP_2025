<?php

declare(strict_types=1);

namespace App\Validator;

use Exception;

class ParenthesisValidator
{
    protected array $checkedChars = ['(', ')'];

    protected string $string;

    public function __construct(string $str)
    {
        $this->string = $str;
    }

    /**
     * @throws Exception
     */
    public function isValidate(): bool
    {
        return $this->isCorrectStr($this->string);
    }

    /**
     * @throws Exception
     */
    protected function isCorrectStr(string $string): bool
    {
        $strLen = \mb_strlen($string);

        if ($strLen <= 1) {
            throw new Exception('Мало символов для проверки', 400);
        }

        return $this->isCorrectChars($string, $this->checkedChars, 0, 1, $strLen - 1);
    }

    /** Проверяет корректность пар символов
     * @param string $string
     * @param array $checkedChars
     * @param int $startIndex
     * @param int $nextIndex
     * @param int $lastIndex
     * @return bool
     */
    protected function isCorrectChars(
        string $string, array $checkedChars, int $startIndex, int $nextIndex, int $lastIndex
    ): bool
    {
        if ($this->isEqualChars($string, $checkedChars, $startIndex, $nextIndex)) {
            return (($startIndex + 2) > $lastIndex) ||
                $this->isCorrectChars(
                    $string, $checkedChars, $startIndex + 2, $nextIndex + 2, $lastIndex
                );
        } elseif ($this->isEqualChars($string, $checkedChars, $startIndex, $lastIndex)) {
            return $this->isCorrectChars(
                $string, $checkedChars, $startIndex + 1, $nextIndex + 1, $lastIndex - 1
            );
        }

        return false;
    }

    /** Проверяет что пары символов идентичны.
     * @param string $string
     * @param array $checkedChars
     * @param int $leftIndex
     * @param int $rightIndex
     * @return bool
     */
    protected function isEqualChars(string $string, array $checkedChars, int $leftIndex, int $rightIndex): bool
    {
        $charsCompared = [\mb_substr($string, $leftIndex, 1), \mb_substr($string, $rightIndex, 1)];
        $intersectChars = \array_intersect($charsCompared, $checkedChars);

        return $checkedChars === $intersectChars;
    }
}
