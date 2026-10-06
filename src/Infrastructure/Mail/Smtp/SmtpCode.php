<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Smtp;

enum SmtpCode: int
{
    case Ok = 250;
    case TemporarilyUnavailable = 421;
    case LocalError = 451;
}
