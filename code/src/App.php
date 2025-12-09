<?php

declare(strict_types=1);

namespace App;

class App
{
    private SessionManager $sessionManager;
    private Validator $validator;
    private Response $response;
    private array $session = [];

    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->validator = new Validator();
        $this->response = new Response();
    }

    public function run(): string
    {
        if (!$this->sessionManager->start()) {
            return $this->response->error(503, 'Сервис Redis временно недоступен.');
        }

        $this->session = $this->sessionManager->updateData();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'POST') {
            $string = $_POST['string'] ?? null;
            $validation = $this->validator->validatePostString($string);
            if (!$validation['valid']) {
                return $this->response->error(400, $validation['error']);
            }
        }

        return $this->response->success($this->session);
    }
}
