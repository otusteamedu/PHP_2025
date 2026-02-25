<?php
declare(strict_types=1);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Redis\RedisClient;
use Zibrov\OtusPhp2025\Redis\RedisStorage;
use Zibrov\OtusPhp2025\System\EventSystem;

?>

<p><-- <a href="/">назад</a></p>

<p>Очистить все события в Redis.</p>

<?php

$redisClient = new RedisClient();

$redisStorage = new RedisStorage($redisClient);
$eventSystem = new EventSystem($redisStorage);

$eventSystem->clearAllEvents();
?>

