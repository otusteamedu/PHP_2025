<?php
/**
 * Класс для валидации входных данных
 */
class Validator 
{
    /**
     * Проверяет строку на пустоту
     * 
     * @param string $str Строка для проверки
     * @return bool Результат проверки
     * @throws Exception Если строка пуста
     */
    public function validateLength($str) 
    {
        if(empty($str)) {
            throw new Exception("string is required");
        }
        
        return true;
    }
    
    /**
     * Проверяет правильность расстановки скобок в строке
     * 
     * @param string $str Строка для проверки
     * @return bool Результат проверки
     * @throws Exception Если синтаксис неверный
     */
    public function validateBrackers($str) 
    {
        $this->validateLength($str);
        
        $openBreackers = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            if($str[$i] == '(') {
                $openBreackers++;
            } elseif($str[$i] == ')') {
                $openBreackers--;
            }

            if($openBreackers < 0) {
                throw new Exception("Wrong syntax on $i");
            }
        }
        
        if($openBreackers != 0) {
            throw new Exception("Wrong syntax on $i");
        }
        
        return true;
    }
}
