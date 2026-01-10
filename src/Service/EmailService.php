<?php

namespace App\Service;

class EmailService
{
    public function sendNotification(string $email, string $subject, string $message): void
    {
        // В реальном приложении здесь была бы отправка email
        // Для демонстрации просто логируем
        echo "Sending email to: {$email}\n";
        echo "Subject: {$subject}\n";
        echo "Message: {$message}\n";
        echo "---\n";
        
        // Имитация задержки обработки
        sleep(rand(2, 5));
    }
}