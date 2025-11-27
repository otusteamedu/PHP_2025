<?php
namespace App;

class Validation
{
    public static function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Некорректный Email";
        }

        $domain = substr(strrchr($email, "@"), 1);
        
        return checkdnsrr($domain, "MX") ? "Корректный Email" : "Некорректный Email";
    }
}
?>