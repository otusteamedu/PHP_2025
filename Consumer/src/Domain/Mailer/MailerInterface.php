<?php

namespace Consumer\Domain\Mailer;

interface MailerInterface
{
    public function mail(
        string $subject,
        string $body,
        string $to,
        ?string $from
    );
}