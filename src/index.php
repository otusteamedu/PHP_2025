<?php

spl_autoload_register();

use Src\Infrastructure\Api\V1\EntrancePoint;

/* $redis = new \Redis();
$redis->connect('redis', 6379);
//$redis->flushDB(); exit();

$keys = $redis->keys('*'); // Получаем все ключи
// Выводим значения ключей
foreach ($keys as $key) {
    $value = $redis->get($key); // Получаем значение по ключу
    echo "<p>Ключ: $key, Значение: $value <a href='?key={$key}'>Проверить обработку</a></p>";
}

if (isset($_GET['key'])) {
    $key = $_GET['key'];
    $value = $redis->get($key); // Получаем значение по ключу
    if($value)
        echo "<p>Ключ: $key в обработке</p>";
    else 
        echo "<p>Ключ: $key уже обработан</p>";
} */

(new EntrancePoint)();