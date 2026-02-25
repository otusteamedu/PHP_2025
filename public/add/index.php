<?php
declare(strict_types=1);

require __DIR__ . '/../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Redis\RedisClient;
use Zibrov\OtusPhp2025\Redis\RedisStorage;
use Zibrov\OtusPhp2025\System\EventSystem;

?>

<p><-- <a href="/">назад</a></p>

<p>Добавим события в Redis.</p>

<?php

$redisClient = new RedisClient();

$redisStorage = new RedisStorage($redisClient);
$eventSystem = new EventSystem($redisStorage);

try {
    $eventSystem->addEvent(1000, ['param1' => 1], '::event1::');
    $eventSystem->addEvent(2000, ['param1' => 2, 'param2' => 2], '::event2::');
    $eventSystem->addEvent(3000, ['param1' => 1, 'param2' => 2], '::event3::');
} catch (JsonException $e) {
    echo 'Ошибка в Redis JSON: ' . $e->getMessage();
}
?>
