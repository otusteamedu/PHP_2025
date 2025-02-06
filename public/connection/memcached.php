<?php

$host = getenv('MEMCACHED_HOST');
$port = 11211;

$message = "MEMCACHED CONNECTION - ";

$memcached = new Memcached();

$memcached->addServer($host, $port);

$status = $memcached->getStats();

if (isset($status["$host:$port"])) {
    $message .= "OK!\r\n";
} else {
    $message .= "FAIL!\r\n";
}

echo $message;

