<?php

declare(strict_types=1);

namespace App;

class Validator
{
    /**
     * Валидация параметра string
     *
     * @param string|null $string Параметр string
     * @return array Результат валидации ['valid' => bool, 'error' => string|null]
     */
    public function validatePostString(string $string = null): array
    {
        if ($string === null) {
            return [
                'valid' => false,
                'error' => 'Отсутствует обязательный параметр string'
            ];
        }

        if (trim($string) === '') {
            return [
                'valid' => false,
                'error' => 'В параметре string отсутствует значение.'
            ];
        }

        if (preg_match('/[()]/', $string)) {
            if (!$this->isValidParentheses($string)) {
                return [
                    'valid' => false,
                    'error' => 'Невалидное значение. Количество открытых и закрытых скобок не совпадает.'
                ];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Проверка валидности скобок в строке
     *
     * @param string $s Входная строка
     * @return bool true если скобки валидны, false в противном случае
     */
    private function isValidParentheses(string $s): bool
    {
        $balance = 0;
        $len = strlen($s);
        
        for ($i = 0; $i < $len; $i++) {
            $char = $s[$i];
            
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
