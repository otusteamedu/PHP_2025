<?php
$memcached = new Memcached();
$memcached->addServer('mysite.local', 11211);
$stats = $memcached->getStats();

if (!$stats) {
    echo "Не удалось подключиться к memcached<br>";
} else {
    echo "Memcached работает!<br>";
}

echo 'memcached stats: <pre>'; print_r($memcached->getStats()); echo '</pre>';
