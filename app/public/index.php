<?php

use App\Service\ParenthesesValidator;
require __DIR__ . '/../vendor/autoload.php';
session_start();
$validator = new ParenthesesValidator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['last_check'] = time();
    $input = $_POST['string'] ?? '';

    if ($validator->isBalanced($input)) {
        http_response_code(200);
        echo 'Строка корректна: всё хорошо';
    } else {
        http_response_code(400);
        echo 'Строка некорректна: всё плохо';
    }
    return;
}

$lastCheck = $_SESSION['last_check'] ?? null;
if ($lastCheck) {
    $lastCheck = DateTimeImmutable::createFromTimestamp($lastCheck)->format('d.m.Y H:i:s');
} else {
  $lastCheck = 'Не производилась';
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Проверка скобок</title>
</head>
<body>
<form method="post" action="/">
    <label>
        Строка со скобками:
        <input type="text" name="string" style="width: 600px" value="">
    </label>
    <button type="submit">Проверить</button>
</form>
<div>
  Сервер: <?=$_SERVER['SERVER_ADDR']?><br>
  Последняя проверка: <?=$lastCheck?>
</div>
</body>
</html>
