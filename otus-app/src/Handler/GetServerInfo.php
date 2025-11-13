<?php

namespace App\Handler;

class GetServerInfo
{
    public function getInfo(): string
    {
        $message = "Проверка подключений" . "<br>";
        $message .= $_SERVER['SERVER_ADDR'] . "<br>" . $_SERVER['HOSTNAME'] . "<br>";

        return $message;
    }
}
