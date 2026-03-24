<?php

require 'vendor/autoload.php';

use App\Services\QueueService;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dateFrom = $_POST['date_from'] ?? '';
    $dateTo = $_POST['date_to'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($dateFrom && $dateTo && $email) {
        try {
            $queueService = new QueueService();
            $task = [
                'type' => 'statement_generation',
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $queueService->push($task);
            $message = "Запрос принят в обработку. Вы получите уведомление на $email после завершения.";
        } catch (Exception $e) {
            $message = "Ошибка при отправке запроса: " . $e->getMessage();
        }
    } else {
        $message = "Пожалуйста, заполните все поля.";
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генерация выписки</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.5rem; box-sizing: border-box; }
        button { padding: 0.5rem 1rem; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px; }
        button:hover { background-color: #0056b3; }
        .message { padding: 1rem; background: #e0f7fa; border-left: 5px solid #006064; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>Заказ банковской выписки</h1>
    
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="date_from">Дата начала периода:</label>
            <input type="date" id="date_from" name="date_from" required>
        </div>
        
        <div class="form-group">
            <label for="date_to">Дата окончания периода:</label>
            <input type="date" id="date_to" name="date_to" required>
        </div>

        <div class="form-group">
            <label for="email">Email для оповещения:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <button type="submit">Заказать выписку</button>
    </form>
</body>
</html>
