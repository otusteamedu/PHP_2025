<?php
$redis = new Redis();

try {
    $redis->connect('redis', 6379);
    echo "✅ Подключились к редис<br>";
    $redis->set("test_key", "Hello Redis");
    echo "Redis test value: " . $redis->get("test_key") . "<br><hr>";
} catch (Exception $e) {
    echo "❌ Redis connection failed: " . $e->getMessage();
}
