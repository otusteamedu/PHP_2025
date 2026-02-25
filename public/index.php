<?php
declare(strict_types=1);

require __DIR__ . '/../composer/vendor/autoload.php';
?>
<p>Пример работы с "Redis"</p>

<ul>
    <li><a href="/add/">Добавляем события</a></li>
    <li><a href="/search/">Находим наиболее подходящее событие</a></li>
    <li><a href="/clear/">Очищаем все события</a></li>
</ul>

<?php /*





use Predis\Client as RedisClient;
use Zibrov\OtusPhp2025\Redis\RedisStorage;
use Zibrov\OtusPhp2025\System\EventSystem;


//$redis = new Predis\Client('tcp://localhost:6379');
//$redisClient = new Predis\Client('tcp://redis:6379');
//$redis = new Predis\Client('tcp://0.0.0.0:6379');
//$redis = new Predis\Client('tcp://127.0.0.1:6379');


$redisClient = new RedisClient([
    'scheme' => 'tcp',
    'host'   => 'redis',
    'port'   => 6379,
]);

$redisClient->set('foo', 'bar');
//$redisClient->set('foo1', 'bar1');
echo $redisClient->get('foo').PHP_EOL;
echo $redisClient->get('foo1').PHP_EOL;
echo $redisClient->get('foo2').PHP_EOL;






//
//
//
//
//
//
//
//
//
//Autoloader::register();
//
//
//$obRedis = new Redis();
//$obRedis->connect('redis', 6379);
//$obRedis->set('key', 'test-redis');
//echo $obRedis->get('key') . PHP_EOL;
//$obRedis->del('key');
//echo $obRedis->get('key') . PHP_EOL;
//
////$redisClient = new RedisClient('/usr/local/etc/redis/redis.conf');
//
//$redisClient = new RedisClient([
//    'scheme' => 'tcp',
////    'host'   => '0.0.0.0',
//    'host'   => '127.0.0.1',
////    'host'   => '10.0.0.1',
//    'port'   => 6379,
//]);
//
////$redisClient = new RedisClient();
//
//
////try {
//    $redisClient->set('foo', 'bar');
//    $value = $redisClient->get('foo');
//    echo '<pre>',var_dump($value),'</pre>';
////} catch (Exception $e) {
////    $requestInfo = $redisClient->transaction();
////    echo '<pre>',var_dump($requestInfo),'</pre>';
////    echo '<pre>',var_dump($redisClient->isConnected()),'</pre>';
////}
//
//











//$redisClient = new RedisClient([
////    'scheme' => 'tcp',
////    'host'   => '0.0.0.0',
//    'host'   => '127.0.0.1',
////    'host'   => '10.0.0.1',
//    'port'   => 6379,
//]);

//$redisClient = new RedisClient();

//echo '<pre>',var_dump($redisClient->isConnected()),'</pre>';

//$redisStorage = new RedisStorage($redisClient);
//
////
//$eventSystem = new EventSystem($redisStorage);
//
//echo '<pre>',var_dump(get_class($eventSystem)),'</pre>';
//
//// Добавляем события
//$eventSystem->addEvent(1000, ['param1' => 1], '::event1::');
////$eventSystem->addEvent(2000, ['param1' => 2, 'param2' => 2], '::event2::');
////$eventSystem->addEvent(3000, ['param1' => 1, 'param2' => 2], '::event3::');


*/
?>

