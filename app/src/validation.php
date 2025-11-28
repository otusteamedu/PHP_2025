<?php
namespace App;

// Класс для валидации email
class Validation
{
    // Проверка формата и DNS запись MX
    public static function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Некорректный Email";
        }

        // Получаю домен из email (после знака @)
        $domain = substr(strrchr($email, "@"), 1);
        
        return checkdnsrr($domain, "MX") ? "Корректный Email" : "Некорректный Email";
    }
}
?>