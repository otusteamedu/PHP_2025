<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailpit;

enum MailpitChaosTrigger: string
{
    case Sender = 'Sender';
    case Recipient = 'Recipient';
    case Authentication = 'Authentication';
}
