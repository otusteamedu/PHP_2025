<?php

declare(strict_types=1);

require_once __DIR__ . '/functions/auth.php';
require_once __DIR__ . '/functions/validate.php';
require_once __DIR__ . '/functions/response.php';

class App
{
    private array $session = [];

    public function run(): string
    {
        if (!startSessionSafe()) {
            return buildErrorResponse(503, 'Сервис Redis временно недоступен.');
        }

        $this->session = updateSessionData();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'POST') {
            $validation = validatePostString($_POST);

            if (!$validation['valid']) {
                return buildErrorResponse(400, $validation['error']);
            }

            return buildSuccessResponse($this->session);
        }

        return buildSuccessResponse($this->session);
    }
}
