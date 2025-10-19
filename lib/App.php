<?php

declare(strict_types=1);

require_once __DIR__.'/EmailValidator.php';

class App
{
    private EmailValidator $validator;

    public function __construct()
    {
        $this->validator = new EmailValidator;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $emailsInput = '';
        $results = [];

        if ($method === 'POST' && !empty($_POST['emails'])) {
            $emailsInput = (string) $_POST['emails'];
            $emails = preg_split('/[\s,]+/', $emailsInput, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($emails as $email) {
                $email = trim($email);
                if ($email !== '') {
                    [$isValid, $details] = $this->validator->validate($email);
                    $results[] = [
                        'email' => $email,
                        'isValid' => $isValid,
                        'details' => $details,
                    ];
                }
            }
        }

        require __DIR__ . '/../views/email_check.php';
    }
}
