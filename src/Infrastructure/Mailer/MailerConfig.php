<?php
declare(strict_types=1);

namespace App\Infrastructure\Mailer;

class MailerConfig
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $fromEmail,
        public readonly string $fromName,
    ) {}
}
