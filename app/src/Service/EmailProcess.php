<?php
namespace App\Service;

use App\Domain\EmailResult;
use App\Validation;

// Получается дополнительный класс для обработки emails
class EmailProcess
{
    // Читает файл emails.txt и делает валидацию email ящиков
    public static function processEmails($filename): EmailResult
    {
        // Чтение файла
        $emails = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $results = [];

        // Валидация и запись результата
        foreach ($emails as $email) {
            $results[$email] = Validation::validateEmail($email);
        }
        
        // Получаю объект с результатами
        return new EmailResult($results);
    }
}
?>