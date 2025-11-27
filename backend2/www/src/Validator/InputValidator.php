<?php
namespace App\Validator;

class InputValidator
{
    public function validate(string $input): void
    {
        $input = trim($input);
        
        if ($input === '') {
            throw new \InvalidArgumentException("Пустая строка");
        }
        
        $balance = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            
            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    throw new \InvalidArgumentException("Неверные скобки");
                }
            } else {
                throw new \InvalidArgumentException("Недопустимый символ: '{$char}'");
            }
        }
        
        if ($balance !== 0) {
            throw new \InvalidArgumentException("Неверные скобки");
        }
    }
}
?>