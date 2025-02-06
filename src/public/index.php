<?php
declare(strict_types=1);

$obRedis = new Redis();
try {
    $obRedis->connect(getenv('REDIS_HOST'), 6379);
    $obRedis->set('test_key', 'Test redis');
    echo $obRedis->get('test_key') . PHP_EOL;
} catch (RedisException $e) {
    die('Error redis: ' . $e->getMessage());
}
?>
    <br>
<?php
$obMemcached = new Memcached();
try {
    $obMemcached->addServer(getenv('MEMCACHED_HOST'), 11211);
    $obMemcached->set('test_key', 'Test memcached');
    echo $obMemcached->get('key') . PHP_EOL;
} catch (MemcachedException $e) {
    die('Error memcached: ' . $e->getMessage());
}
?>
    <br>
<?php
$obMysqli = new mysqli('mysql', 'admin', 'password123', 'database', 3306);
if ($obMysqli->connect_error) {
    die('Error connecting to MySQL: ' . $obMysqli->connect_error);
}
echo 'Connection to MySQL server established!';

$obMysqli->close();

phpinfo();
