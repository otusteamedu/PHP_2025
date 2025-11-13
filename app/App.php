<?php

declare(strict_types=1);

namespace App;

final class App
{
    private RequestHandler $handler;

    public function __construct()
    {
        $validator = new BracketValidator();
        $responseSender = new ResponseSender();
        $this->handler = new RequestHandler($validator, $responseSender);
    }

    /**
     * Принимает данные из массива $_POST и возвращает результат обработки
     */
    public function run(): string
    {
        return $this->handler->handle($_POST);
    }
}

