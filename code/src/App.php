<?php

declare(strict_types=1);

namespace App;

use App\Service\EmailValidator;
use App\Http\Request;
use App\Http\Response;

class App
{
    private Request $request;
    private Response $response;
    private EmailValidator $emailValidator;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->emailValidator = new EmailValidator();
    }

    public function run(): string
    {
        if (!$this->request->isPost()) {
            return $this->response->error(405, 'Метод не разрешен. Используйте POST запрос.');
        }

        if ($this->request->getPath() !== '/emails') {
            return $this->response->error(404, 'Маршрут не найден. Используйте POST /emails');
        }

        if (!$this->request->isValidEmailsBody()) {
            return $this->response->error(400, 'Необходимо передать массив email-адресов в формате JSON.');
        }

        $emails = $this->request->getBody();
        $result = $this->emailValidator->verifyEmails($emails);

        return $this->response->success($result);
    }
}
