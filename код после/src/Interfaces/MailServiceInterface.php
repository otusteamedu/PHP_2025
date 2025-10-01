<?php

namespace App\Interfaces;

interface MailServiceInterface {
    public function sendWelcomeEmail(string $email, string $name): void;
    public function sendNotificationEmail(string $email, string $message): void;
}