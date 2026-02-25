<?php
declare(strict_types=1);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Redis\RedisClient;
use Zibrov\OtusPhp2025\Redis\RedisStorage;
use Zibrov\OtusPhp2025\System\EventSystem;

?>

<p><-- <a href="/">назад</a></p>

<p>Найти наиболее подходящее событие в Redis.</p>

<?php

$redisClient = new RedisClient();

$redisStorage = new RedisStorage($redisClient);
$eventSystem = new EventSystem($redisStorage);

$arParams = ['param1' => 1, 'param2' => 2];

try {
    $eventSystem->findSuitableEvent($arParams);
} catch (JsonException $e) {
    echo 'Ошибка в Redis JSON: ' . $e->getMessage();
}
?>

