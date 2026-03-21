<?php

declare(strict_types=1);

namespace Ak\Hw\Models;

use Ak\Hw\Services\EmailService;

class UserReportMessageHandler {
    public function __invoke(array $messageBody): void
    {
        echo " [x] Получен запрос на отчет для user_id: " . $messageBody['user_id'] . "\n";

        $reportData = "Это ваш отчет для user_id: " . $messageBody['user_id'];
        $userEmail = $messageBody['email'];

        echo " [x] Отчет сгенерирован. Отправка email...\n";

        // Отправка email
        $emailService = new EmailService();
        $subject = 'Ваш пользовательский отчет';
        $body = $messageBody['content'];

        if ($emailService->send($userEmail, $subject, $body)) {
            echo " [x] Email успешно отправлен на " . $userEmail . "\n";
        } else {
            echo " [x] Не удалось отправить email на " . $userEmail . "\n";
        }

        echo " [x] Готово.\n";
    }
}
