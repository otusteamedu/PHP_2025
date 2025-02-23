<?php
class BracketValidator {

    private string $input;

    public function __construct(string $input)
    {
        $this->input = $input;
    }
        
    public function isValid():bool
    {
        $stack = [];

        //сначала проверим на очевидные ошибки
        $len = strlen($this->input);

        //на пустую строку
        if ($len === 0) {
            return false;
        }; 

        //проверка что первая скобочка открывающая
        if ($this->input[0] !== '(') {
            return false;
        }
        
        //проверка что в строке только скобочки
        if (preg_match('/^[\(\)]+$/', $this->input) !== 1) {
            return false;
        };

        for ($i=0; $i < $len; $i++) {
            $char = $this->input[$i];

            if ($char === '(') {
                //Добавляем открывающую скобочку в стек
                $stack[] = $char;
            } elseif ($char === ')') {
                //если скобочка закрывающая, проверяем была ли открывающая
                if (empty($stack) || array_pop($stack) !== '(' ) {
                    return false;
                }
            }
        }
        //если к окончанию строки стек пустой , значит скобочки расставлены правильно
        return empty($stack);
    }
}


