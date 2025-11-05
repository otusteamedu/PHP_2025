<?php
echo "Привет, Alex!<br>" . date("Y-m-d H:i:s") . "<br><br>";
// --------
$memcached = new Memcached();
$memcached->addServer('10.10.1.3', 11211);

$key = 'test_key';
$value = 'test_value';

if (!$memcached->set($key, $value)) {
    echo "Ошибка подключения к серверу Memcached или при установке значения.<br>";
} else {
    $retrievedValue = $memcached->get($key);
    if ($memcached->getResultCode() == Memcached::RES_SUCCESS && $retrievedValue === $value) {
        echo "Подключение к Memcached успешно!<br>";
    } else {
        echo "Не удалось подключиться к Memcached или получить значение.<br>";
    }
}
// --------
$servername = "10.10.1.5";
$username = "nobody";
$password = "SECRET_PASS_CHANGE!";
$database = "dbtest";
$port = 3306;

try {
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        echo "Ошибка подключения: " . $conn->connect_error . "<br>";
    } else {
        echo "Подключение к MySQL прошло успешно<br>";
    }
} catch (Exception $e) {
    echo "MySQL: " . $e->getMessage() . "<br>";
}
// --------
$redis = new Redis();

try {
    $redis->connect('10.10.1.2', 6379);
    echo "Успешное подключение к серверу Redis<br>";

    $key = 'test_key';
    $value = 'Hello Redis';

    $redis->set($key, $value);
    $cachedValue = $redis->get($key);

    if ($cachedValue) {
        echo "Данные из Redis для ключа '$key': $cachedValue<br>";
    } else {
        echo "Не удалось получить данные по ключу '$key'<br>";
    }
} catch (RedisException $e) {
    echo "Не удалось подключиться к Redis: " . $e->getMessage() . "<br>";
}
// --------
phpinfo();
