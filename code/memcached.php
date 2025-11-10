<?php
$memcached = new Memcached();
$memcached->addServer('mysite.local', 11211);
$stats = $memcached->getStats();

if (!$stats) {
    echo "Не удалось подключиться к memcached<br>";
    echo 'memcached stats: <pre>'; print_r($stats); echo '</pre>';
} else {
    echo "Memcached работает!<br>";
}
