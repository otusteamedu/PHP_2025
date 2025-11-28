<?php

session_start();

if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 1;
} else {
    $_SESSION['counter']++;
}

$sessionId = session_id();
$container = $_SERVER['HOSTNAME'];
?>
<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hello, Otus!</title>
    </head>
    <body>
        <div class="container">
            <h3 class="container__title">Запрос обработал контейнер:
                <span>container_id=<?= $container ?></span>
            </h3>
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/redis.php';  ?>
            <?php if (isset($containerIdFromRedis)):?>
                <p>redis container_id=<?= $containerIdFromRedis ?></p>
            <?php endif;?>
            <p>Session ID: <?= $sessionId ?></p>
            <p>Session Counter: <?= $_SESSION['counter'] ?></p>
        </div>
    </body>
</html>