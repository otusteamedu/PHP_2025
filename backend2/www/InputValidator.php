<?php

class Validator
{
    public function validate(string $input): void
    {
        $input = trim($input);
        
        if ($input === '') {
            throw new Exception("Пустая строка");
        }
        
        $balance = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            
            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    throw new Exception("Неверные скобки");
                }
            } else {
                throw new Exception("Недопустимый символ");
            }
        }
        
        if ($balance !== 0) {
            throw new Exception("Неверные скобки");
        }
    }
}