<? 

namespace MyTestApp;

Class Validation {

    public static function isValidBrackets($string) {
        // Определяем открывающие и закрывающие скобки
        $brackets = [
            '(' => ')',
            '{' => '}',
            '[' => ']',
        ];
        
        // Инициализируем стек
        $stack = [];

        if(empty($string) OR !($string = preg_replace('/[^\(\),^\{\},^\[\]]/', '', $string)))
            return false;
        
        // Проходим по каждому символу в строке
        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];
    
            // Если встречаем открывающую скобку, помещаем её в стек
            if (array_key_exists($char, $brackets)) {
                array_push($stack, $char);
            } 
            // Если встречаем закрывающую скобку
            else if (in_array($char, $brackets)) {
                // Проверяем, есть ли соответствующая открывающая
                if (empty($stack) || $brackets[array_pop($stack)] !== $char) {
                    return false; // Скобки не парные
                }
            }
        }
        
        // Если стек пуст, значит все скобки корректные
        return empty($stack);
    }

}