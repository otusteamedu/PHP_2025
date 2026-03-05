#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Queue;
use Dotenv\Dotenv;

// Загружаем настройки
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

/**
 * Функция генерации выписки из медкарты
 */
function generateMedicalReport($requestData)
{
    $id = $requestData['id'];
    $patient = $requestData['patient'];
    
    echo "\n📋 [" . date('H:i:s') . "] Обработка запроса {$id}\n";
    echo "   Пациент: {$patient}\n";
    
    // Имитация долгой работы (3-5 секунд)
    echo "   ⏳ Формирование выписки...\n";
    for ($i = 1; $i <= rand(3, 5); $i++) {
        sleep(1);
        echo "   .";
    }
    echo "\n";
    
    // Создаем директорию, если нет
    $storageDir = __DIR__ . '/../storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0777, true);
    }
    
    // Формируем "выписку" - простой текстовый файл
    $filename = $storageDir . "/report_{$id}.txt";
    
    // Содержимое выписки
    $content = "МЕДИЦИНСКАЯ ВЫПИСКА\n";
    $content .= "==================\n\n";
    $content .= "Пациент: {$patient}\n";
    $content .= "Дата запроса: {$requestData['date']}\n";
    $content .= "Номер запроса: {$id}\n\n";
    $content .= "ДИАГНОЗЫ:\n";
    $content .= "- ОРВИ (01.02.2024)\n";
    $content .= "- Гипертония 2 ст (наблюдение)\n\n";
    $content .= "НАЗНАЧЕНИЯ:\n";
    $content .= "- Парацетамол при t>38\n";
    $content .= "- Контроль АД ежедневно\n\n";
    $content .= "Следующий прием: через 2 недели\n";
    
    file_put_contents($filename, $content);
    
    echo "Выписка создана: " . basename($filename) . "\n";
    
    return $filename;
}

/**
 * Отправка email через PHPMailer
 */
function sendEmail($to, $requestId, $file)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';
        
        // От кого и кому
        $mail->setFrom($_ENV['SMTP_FROM'], 'Медицинский центр');
        $mail->addAddress($to);
        
        // Тема и тело письма
        $mail->Subject = "Выписка из медкарты #{$requestId}";
        $mail->isHTML(true);
        $mail->Body = "
            <h2>Здравствуйте!</h2>
            <p>Ваша выписка из медицинской карты готова.</p>
            <p><strong>Номер запроса:</strong> {$requestId}</p>
            <p>Файл прикреплен к письму.</p>
            <hr>
            <p style='color:gray;'>Это автоматическое сообщение</p>
        ";
        
        // Прикрепляем файл
        $mail->addAttachment($file);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        file_put_contents(
            __DIR__ . '/../logs/errors.log',
            date('Y-m-d H:i:s') . " - Ошибка почты: " . $mail->ErrorInfo . "\n",
            FILE_APPEND
        );
        return false;
    }
}

// --- ЗАПУСК ВОРКЕРА ---

echo "===================================\n";
echo "МЕДИЦИНСКИЙ ВОРКЕР ЗАПУЩЕН\n";
echo "Очередь: " . $_ENV['RABBITMQ_QUEUE'] . "\n";
echo "PID: " . getmypid() . "\n";
echo "===================================\n";

// Подключаемся к очереди
$queue = new Queue($_ENV['RABBITMQ_QUEUE']);

// Начинаем слушать очередь
$queue->consumeRequests(function($requestData) {
    
    // Шаг 1: Генерируем выписку
    $file = generateMedicalReport($requestData);
    
    // Шаг 2: Отправляем email
    $sent = sendEmail(
        $requestData['email'],
        $requestData['id'],
        $file
    );
    
    if ($sent) {
        echo "Email отправлен на {$requestData['email']}\n";
        return true; // Успешно - подтверждаем обработку
    } else {
        echo "Ошибка отправки email!\n";
        return false; // Ошибка - вернем в очередь
    }
});