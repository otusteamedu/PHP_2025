<?php

namespace App\Interface;

interface SessionManagerInterface {
    public function generateCsrfToken(): string;
    public function verifyCsrfToken(string $token): bool;
}