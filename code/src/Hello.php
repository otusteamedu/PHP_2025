<?php
namespace App;

class Hello {
    public static function hi(): string {
        return "Привет из App\\Hello. Проверка автозагрузки файлов с composer!";
    }
}