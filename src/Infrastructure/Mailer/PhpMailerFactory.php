<?php
declare(strict_types=1);

namespace App\Infrastructure\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerFactory
{
    public function __construct(
        private readonly MailerConfig $config
    ) {}

    public function create(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $this->config->host;
        $mail->Port = $this->config->port;
        $mail->SMTPAuth = false;

        $mail->setFrom($this->config->fromEmail, $this->config->fromName);

        return $mail;
    }
}
