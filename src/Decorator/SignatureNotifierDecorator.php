<?php

declare(strict_types=1);

namespace App\Decorator;

final class SignatureNotifierDecorator extends EmailNotifierDecorator
{
    public function __construct(EmailNotifier $wrapped, private string $signature)
    {
        parent::__construct($wrapped);
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $bodyWithSignature = $body . "\n\n" . $this->signature;
        return parent::send($to, $subject, $bodyWithSignature);
    }
}
