<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Notification;

use MkdBot\Domain\Enum\ProposalType;
use MkdBot\Domain\Interface\ProposalNotifierInterface;
use PHPMailer\PHPMailer\Exception as PhpMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

/**
 * Уведомление о предложениях через PHPMailer (SMTP)
 *
 * Отправляет HTML-письмо на список адресов из конфигурации.
 * Ошибка отправки НЕ пробрасывается — логируется как ERROR,
 * чтобы не ломать основной поток подтверждения предложения.
 */
class PhpMailerProposalNotifier implements ProposalNotifierInterface
{
    public function __construct(
        private readonly string $smtpHost,
        private readonly int $smtpPort,
        private readonly string $smtpUser,
        private readonly string $smtpPassword,
        private readonly string $smtpFromEmail,
        private readonly string $smtpFromName,
        private readonly string $notifyEmails,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function notify(ProposalType $type, string $subject, string $content, string $authorName, int $authorId): void
    {
        // Парсим список адресов получателей
        $recipients = array_filter(array_map('trim', explode(',', $this->notifyEmails)));

        if (empty($recipients)) {
            $this->logger->warning('Список адресов для уведомлений о предложениях пуст — уведомление не отправлено');
            return;
        }

        $mail = new PHPMailer(true);

        try {
            // Настройка SMTP
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->Port = $this->smtpPort;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUser;
            $mail->Password = $this->smtpPassword;

            // Автоопределение шифрования по порту
            if ($this->smtpPort === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->smtpFromEmail, $this->smtpFromName);

            foreach ($recipients as $email) {
                $mail->addAddress($email);
            }

            $typeLabel = $type === ProposalType::Feature ? 'Новый функционал' : 'Новое предложение';
            $mail->Subject = "{$typeLabel}: {$subject}";

            $mail->isHTML(true);
            $mail->Body = $this->buildHtmlBody($type, $subject, $content, $authorName, $authorId);

            $mail->send();

            $this->logger->info("Уведомление о предложении отправлено на " . count($recipients) . " адресов");
        } catch (PhpMailerException $e) {
            $this->logger->error("Ошибка отправки уведомления о предложении: " . $e->getMessage());
        }
    }

    /**
     * Формирует HTML-тело письма с данными предложения
     */
    private function buildHtmlBody(ProposalType $type, string $subject, string $content, string $authorName, int $authorId): string
    {
        $typeLabel = $type === ProposalType::Feature ? 'Функционал' : 'Предложение';
        $date = date('d.m.Y H:i');

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">
<h2 style="color: #2c3e50;">{$typeLabel}: {$this->escapeHtml($subject)}</h2>
<table style="border-collapse: collapse; width: 100%; max-width: 600px;">
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold; width: 150px;">Тип</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$typeLabel}</td>
</tr>
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Тема</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$this->escapeHtml($subject)}</td>
</tr>
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Содержание</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$this->escapeHtml($content)}</td>
</tr>
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Автор</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$this->escapeHtml($authorName)}</td>
</tr>
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">ID автора</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$authorId}</td>
</tr>
<tr>
<td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9; font-weight: bold;">Дата</td>
<td style="padding: 8px; border: 1px solid #ddd;">{$date}</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    /**
     * Экранирует HTML-спецсимволы
     */
    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
