<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Exception;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReportMailerException extends \RuntimeException
{
    public static function transportError(TransportExceptionInterface $e): self
    {
        return new self('SMTP transport error: ' . $e->getMessage(), $e->getCode());
    }
}
