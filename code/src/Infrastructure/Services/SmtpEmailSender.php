<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Interfaces\EmailSenderInterface;

class SmtpEmailSender implements EmailSenderInterface
{
    public function __construct(
        private readonly string $host = 'mailhog',
        private readonly int $port = 1025,
        private readonly ?string $username = null,
        private readonly ?string $password = null,
        private readonly string $fromEmail = 'noreply@bank-reports.local',
        private readonly string $fromName = 'Банковская система отчётности'
    ) {}

    public function send(string $to, string $subject, string $body): bool
    {
        try {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 30);

            if (!$socket) {
                echo "SMTP connection failed: {$errstr} ({$errno})\n";
                return false;
            }

            $this->getResponse($socket);
            $this->sendCommand($socket, "EHLO localhost");

            if ($this->username && $this->password) {
                $this->sendCommand($socket, "AUTH LOGIN");
                $this->sendCommand($socket, base64_encode($this->username));
                $this->sendCommand($socket, base64_encode($this->password));
            }

            $this->sendCommand($socket, "MAIL FROM:<{$this->fromEmail}>");
            $this->sendCommand($socket, "RCPT TO:<{$to}>");
            $this->sendCommand($socket, "DATA");

            $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $headers .= "To: {$to}\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Date: " . date('r') . "\r\n";

            fwrite($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
            $this->getResponse($socket);

            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            return true;
        } catch (\Throwable $e) {
            echo "Email sending failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function sendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->getResponse($socket);
    }

    private function getResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
}
