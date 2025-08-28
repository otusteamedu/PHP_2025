<?php

namespace App\Validation;

use App\Interface\RequestValidatorInterface;
use App\Interface\SessionManagerInterface;

class PostRequestValidator implements RequestValidatorInterface {
    private SessionManagerInterface $sessionManager;

    public function __construct(SessionManagerInterface $sessionManager) {
        $this->sessionManager = $sessionManager;
    }

    public function validateRequest(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'Method Not Allowed', 'status' => 405];
        }

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!$this->sessionManager->verifyCsrfToken($csrf_token)) {
            return ['error' => 'Invalid CSRF token', 'status' => 403];
        }

        $emails = $_POST['emails'] ?? [];
        if (!is_array($emails) || empty($emails)) {
            return ['error' => 'No emails provided', 'status' => 400];
        }

        return ['emails' => $emails, 'status' => 200];
    }
}