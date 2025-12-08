<?php

declare(strict_types=1);

/**
 * Валидация POST-параметра string
 *
 * @param array $post Массив POST данных
 * @return array Результат валидации ['valid' => bool, 'error' => string|null]
 */
function validatePostString(array $post): array
{
    if (!array_key_exists('string', $post)) {
        return [
            'valid' => false,
            'error' => 'Отсутствует обязательный параметр string'
        ];
    }

    $input = (string)$post['string'];

    if (trim($input) === '') {
        return [
            'valid' => false,
            'error' => 'В параметре string отсутствует значение.'
        ];
    }

    if (preg_match('/[()]/', $input)) {
        if (!isValidParentheses($input)) {
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
function isValidParentheses(string $s): bool
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
