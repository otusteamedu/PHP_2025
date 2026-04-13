<?php

declare(strict_types=1);

namespace App\Decorator;

interface EmailNotifier
{
    public function send(string $to, string $subject, string $body): bool;
}
