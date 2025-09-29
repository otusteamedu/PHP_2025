<?php

require_once __DIR__.'/App.php';

$app = new App;

try {
    echo $app->run();
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 400);
    echo 'Ошибка: '.$e->getMessage();
}
