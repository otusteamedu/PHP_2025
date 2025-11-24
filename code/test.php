<?php
echo "Привет, Alex!<br>" . date("Y-m-d H:i:s") . "<br><br>";
// --------
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);

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
$servername = "mysql";
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
// Подключение к Redis через Sentinel для поддержки failover
$redis = new Redis();
$sentinel = new Redis();

$sentinelHosts = ['sentinel1', 'sentinel2', 'sentinel3'];
$sentinelPort = 26379;
$masterName = 'mymaster';
$connectSuccess = false;

foreach ($sentinelHosts as $sentinelHost) {
    try {
        $sentinel->connect($sentinelHost, $sentinelPort, 2.0);
        $master = $sentinel->rawCommand('SENTINEL', 'get-master-addr-by-name', $masterName);
        
        if ($master && is_array($master) && count($master) >= 2) {
            $masterHost = $master[0];
            $masterPort = $master[1];
            
            $redis->connect($masterHost, $masterPort, 2.0);
            $redis->auth('secret_pass_123');
            
            echo "Успешное подключение к мастеру Redis через Sentinel: $masterHost:$masterPort<br>";
            $connectSuccess = true;
            break;
        }
    } catch (RedisException $e) {
        echo "Ошибка подключения к Sentinel $sentinelHost: " . $e->getMessage() . "<br>";
        continue;
    }
}

if (!$connectSuccess) {
    echo "Не удалось подключиться к Redis через Sentinel. Попытка прямого подключения...<br>";
    try {
        $redis->connect('redis1', 6379, 2.0);
        $redis->auth('secret_pass_123');
        echo "Успешное прямое подключение к Redis<br>";
        $connectSuccess = true;
    } catch (RedisException $e) {
        echo "Не удалось подключиться к Redis: " . $e->getMessage() . "<br>";
    }
}

if ($connectSuccess) {
    $key = 'test_key';
    $value = 'Hello Redis';

    $redis->set($key, $value);
    $cachedValue = $redis->get($key);

    if ($cachedValue) {
        echo "Данные из Redis для ключа '$key': $cachedValue<br>";
    } else {
        echo "Не удалось получить данные по ключу '$key'<br>";
    }
}
// --------
phpinfo();