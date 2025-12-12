<?php

declare(strict_types=1);

namespace App;

use App\Service\EmailValidator;
use App\Http\Response;

class App
{
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function run(): string
    {
        $emails = [
            'user@example.com',
            'admin@mail.ru',
            'user@@example..com',
            'test@localhost.ru',
            'support@gmail.com',
        ];

        $emailValidator = new EmailValidator();

        $result = $emailValidator->verifyEmails($emails);

        return $this->response->send(200, json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}
