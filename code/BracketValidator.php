<?php

namespace App;

class BracketValidator {

    private string $input;

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function isValid(): array
    {
        if (!$this->isFirstCharOpenBracket()) {
            return ['error' => 'Скобочки не согласованы: первый символ не открывающая скобочка'];
        } elseif (!$this->isOnlyBracketsInStr()) {
            return ['error' => 'Скобочки не согласованы: в строке есть другие символы кроме круглых скобочек'];
        } elseif (!$this->isBracketsMatched()) {
            return ['error' => 'Скобочки не согласованы: открывающие скобочки не соответствуют закрывающим'];
        } elseif ($this->isBracketsMatched()) {
            return ['success' => 'Все Ок. Скобочки согласованы'];
        } else {
            return ['error' => 'Неизвестная ошибка'];    
        }
    }

    protected function isFirstCharOpenBracket():bool
    {
        return isset($this->input[0]) && $this->input[0] === '(';
    }

    protected function isOnlyBracketsInStr():bool
    {
        return preg_match('/^[\(\)]+$/', $this->input) === 1;
    }

    protected function isBracketsMatched():bool
    {
        $stack = [];
        for ($i=0; $i < strlen($this->input); $i++) {
            $char = $this->input[$i];

            if ($char === '(') {
                //Добавляем открывающую скобочку в стек
                $stack[] = $char;
            } elseif ($char === ')') {
                //если скобочка закрывающая, проверяем была ли открывающая
                if (empty($stack) || array_pop($stack) !== '(' ) {
                    return false;
                };
            } else {
                return false;
            } 
        }
        //если к окончанию строки стек пустой , значит скобочки расставлены правильно
        return empty($stack);
    }
}


