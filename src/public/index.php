<?php
declare(strict_types=1);

$obRedis = new Redis();
$obRedis->connect('redis', 6379);
$obRedis->set('key', 'test-redis');
echo $obRedis->get('key') . PHP_EOL;
?>
    <br>
<?php
$obMemcached = new Memcached();
$obMemcached->addServer('memcached', 11211);
$obMemcached->set('key', 'test-memcached');
echo $obMemcached->get('key') . PHP_EOL;
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
