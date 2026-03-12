<?php

declare(strict_types=1);

namespace Queues\Application\Interfaces;

interface MailerInterface
{
    public function send(string $to, string $subject, string $body): void;
}
