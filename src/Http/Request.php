<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @return string
     */
    public function getEmailsInput(): string
    {
        return trim($_POST['emails'] ?? '');
    }
}
