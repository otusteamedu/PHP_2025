<?php declare(strict_types=1);

namespace App\Bankhistory\Sendler;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSendler
{
	private array $config;
	public function __construct()
	{
		// Конфигурация SMTP
		$this->config = [
			'smtp_host' => $_ENV['SMTP_HOST'],
			'smtp_username' => $_ENV['SMTP_USERNAME'],
			'smtp_password' => $_ENV['SMTP_PASSWORD'],
			'smtp_port' => $_ENV['SMTP_PORT'],
			'smtp_secure' => $_ENV['SMTP_SECURE'],
		];
	}

	public function send(string $to, string $from, string $subject, string $message): bool
	{
		$mail = new PHPMailer(true);

		try {
			// Настройки сервера
			$mail->isSMTP();
			$mail->Host = $this->config['smtp_host'];
			$mail->SMTPAuth = true;
			$mail->Username = $this->config['smtp_username'];
			$mail->Password = $this->config['smtp_password'];
			$mail->SMTPSecure = $this->config['smtp_secure'];
			$mail->Port = $this->config['smtp_port'];

			// Настройки email
			$mail->setFrom($from);
			$mail->addAddress($to);
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->isHTML(true);

			$mail->send();

			return true;

		} catch (Exception $e) {

			return false;

		}
	}
}