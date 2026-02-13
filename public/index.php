<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/../src/App.php';

$app = new App;

try {
    echo $app->run();
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 400);
    echo 'Ошибка: '.$e->getMessage();
}
