<?php declare(strict_types=1);

namespace App\Validations;

use App\Exceptions\ValidationException;

class StringVerify
{
    /**
     * Проверяет строку на наличие корректных скобок.
     *
     * @param array $post
     * @param string $key
     * @return string
     * @throws ValidationException
     */
    public function validate(array $post, string $key): string
    {
        if (!isset($post[$key]) || trim($post[$key]) === '') {
            throw new ValidationException("$key обязательный параметр");
        }

        $value = $post[$key];
        $this->validateParentheses($value);

        return $value;
    }

    /**
     * Проверяет корректность расстановки скобок в строке.
     *
     * @param string $value
     * @throws ValidationException
     */
    private function validateParentheses(string $value): void
    {
        $opening = [')' => '('];
        $parentheses = [];

        foreach (str_split($value) as $char) {
            if ($char === '(') {
                $parentheses[] = $char;
            } elseif ($char === ')') {
                if (empty($parentheses) || array_pop($parentheses) !== $opening[$char]) {
                    throw new ValidationException(
                        "Всё плохо ¯\_(ツ)_/¯ некорректная расстановка скобок в строке"
                    );
                }
            }
        }

        if (!empty($parentheses)) {
            throw new ValidationException("Не все скобки закрыты");
        }
    }
}