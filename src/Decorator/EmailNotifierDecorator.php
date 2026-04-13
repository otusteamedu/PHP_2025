<?php

declare(strict_types=1);

namespace App\Decorator;

abstract class EmailNotifierDecorator implements EmailNotifier
{
    public function __construct(protected EmailNotifier $wrapped)
    {
    }

    public function send(string $to, string $subject, string $body): bool
    {
        return $this->wrapped->send($to, $subject, $body);
    }
}
