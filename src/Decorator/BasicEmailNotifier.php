<?php

declare(strict_types=1);

namespace App\Decorator;

final class BasicEmailNotifier implements EmailNotifier
{
    public function send(string $to, string $subject, string $body): bool
    {
        echo "[BasicEmailNotifier] Sending email to {$to}\n";
        echo "Subject: {$subject}\n";
        echo "Body: {$body}\n";

        return true;
    }
}
