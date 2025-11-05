<?php

// Check if Memcached extension is loaded
if (extension_loaded('memcached')) {
    echo "<h1>Memcached extension is loaded.</h1>";

    // Attempt to connect to Memcached server
    $memcached = new Memcached();
    $memcached->addServer('memcached', 11211); // Use the service name from docker-compose

    if ($memcached->getStats()) {
        echo "<h1>Successfully connected to Memcached server.</h1>";
        echo "<pre>";
        print_r($memcached->getStats());
        echo "</pre>";
    } else {
        echo "<h1>Could not connect to Memcached server.</h1>";
    }
} else {
    echo "<h1>Memcached extension is NOT loaded.</h1>";
}

// Check if Redis extension is loaded
if (extension_loaded('redis')) {
    echo "<h1>Redis extension is loaded.</h1>";

    try {
        // Attempt to connect to Redis server
        $redis = new Redis();
        $redis->connect('redis', 6379); // Use the service name from docker-compose

        if ($redis->ping()) {
            echo "<h1>Successfully connected to Redis server.</h1>";
            echo "<pre>";
            echo "Redis server is running.";
            echo "</pre>";
        } else {
            echo "<h1>Could not connect to Redis server.</h1>";
        }
    } catch (RedisException $e) {
        echo "<h1>Could not connect to Redis server: " . $e->getMessage() . "</h1>";
    }
} else {
    echo "<h1>Redis extension is NOT loaded.</h1>";
}
