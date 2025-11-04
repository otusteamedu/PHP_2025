<?php
namespace App\Classes;
class TelegramService {
    public function notify(string $phone) {
        $msg = "Отчет для номера $phone успешно сгенерирован";
        file_get_contents($_ENV['TELEGRAM_API'] . "&text=" . urlencode($msg));
    }
}