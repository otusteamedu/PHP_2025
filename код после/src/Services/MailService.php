<?php

namespace App\Services;

use App\Interfaces\MailServiceInterface;

class MailService implements MailServiceInterface {
    public function sendWelcomeEmail(string $email, string $name): void {
        mail($email, "Welcome", "Hello " . $name);
    }
    
    public function sendNotificationEmail(string $email, string $message): void {
        mail($email, "Notification", $message);
    }
}