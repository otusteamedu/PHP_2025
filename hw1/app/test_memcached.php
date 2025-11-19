<?php
$memcached = new Memcached();

$memcached->addServer('memcached', 11211);

if ($memcached->set('hw1', "Hello Otus, it's my first homework")) {
    echo "✅ Connected to Memcached<br>";
    echo "Memcached значение: " . $memcached->get('hw1');
} else {
    echo "❌ Memcached не завелся";
}
