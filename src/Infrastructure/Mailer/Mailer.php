<?php
declare(strict_types=1);

namespace App\Infrastructure\Mailer;

use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;

class Mailer
{
    public function __construct(
        private readonly PhpMailerFactory $factory,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @throws Exception
     */
    public function sendStatement(string $toEmail, string $statement): void
    {
        $mail = $this->factory->create();
        $mail->addAddress($toEmail);
        $mail->isHTML(false);
        $mail->Subject = 'Bank statement is ready';
        $mail->Body = $statement;

        $mail->send();

        $this->logger->info('Email sent', ['to' => $toEmail]);
    }
}
