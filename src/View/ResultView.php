<?php

namespace Aovchinnikova\Hw14\View;

class ResultView
{
    public function render(int $statusCode, string $message)
    {
        http_response_code($statusCode);
        echo $message;
        echo "<br>Запрос обработал контейнер: " . $_SERVER['HOSTNAME'];
    }
}