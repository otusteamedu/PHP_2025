<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Queue;
use Dotenv\Dotenv;

// Загружаем настройки из .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Простейшая проверка email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Укажите корректный email";
    } else {
        try {
            // Готовим данные для очереди
            $requestData = [
                'id' => uniqid(),           // Уникальный номер запроса
                'email' => $_POST['email'],
                'patient' => $_POST['patient'],
                'date' => date('Y-m-d H:i:s'),
                'status' => 'new'
            ];
            
            // Отправляем в очередь RabbitMQ
            $queue = new Queue($_ENV['RABBITMQ_QUEUE']);
            $queue->sendRequest($requestData);
            
            // Логируем для истории
            file_put_contents(
                __DIR__ . '/../logs/requests.log',
                date('Y-m-d H:i:s') . " - Запрос {$requestData['id']} от {$requestData['email']}\n",
                FILE_APPEND
            );
            
            // Показываем сообщение об успехе
            $success = "Запрос принят! Номер: {$requestData['id']}";
            
        } catch (Exception $e) {
            $error = "Ошибка, попробуйте позже";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Запрос выписки из медкарты</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #e6f3ff;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,100,200,0.2);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #0066cc;
            margin-top: 0;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #0066cc;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #cce6ff;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #0066cc;
        }
        button {
            background: #0066cc;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0052a3;
        }
        .success {
            background: #e3f7e3;
            border: 2px solid #28a745;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            background: #ffe6e6;
            border: 2px solid #dc3545;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info {
            background: #f0f9ff;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #0066cc;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Запрос выписки из медкарты</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"> <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ФИО пациента</label>
                <input type="text" name="patient" required 
                       value="<?= htmlspecialchars($_POST['patient'] ?? '') ?>"
                       placeholder="Иванов Иван Иванович">
            </div>
            
            <div class="form-group">
                <label>Email для получения</label>
                <input type="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="patient@mail.ru">
            </div>
            
            <button type="submit">Запросить выписку</button>
        </form>
        
        <div class="info">
            Выписка формируется до 1 минуты. После готовности придет на email.
            <br><br>
            <strong>Номер запроса сохраните</strong> - он понадобится при обращении в поддержку.
        </div>
    </div>
</body>
</html>