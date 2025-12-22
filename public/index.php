<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\EventManager;
use App\Storage\RedisStorage;

header('Content-Type: application/json');

try {
    $redis = new Redis();
    
    $redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
    $redisPort = $_ENV['REDIS_PORT'] ?? 6379;
    
    $redis->connect($redisHost, $redisPort, 2); // timeout
    
    $storage = new RedisStorage($redis);
    $manager = new EventManager($storage);
    
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    
    // Добавление события
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['priority'], $input['conditions'], $input['event'])) {
        $id = $manager->addEvent(
            (int) $input['priority'],
            $input['conditions'],
            $input['event']
        );
        
        echo json_encode(['success' => true, 'id' => $id]);
    }
    // Поиск события
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['params'])) {
        $params = [];
        foreach ($input['params'] as $param) {
            [$key, $value] = explode('=', trim($param));
            $params[trim($key)] = trim($value);
        }
        
        $result = $manager->findBestEvent($params);
        echo json_encode(['success' => true, 'event' => $result]);
    }
    // Очистка
    elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $manager->clearEvents();
        echo json_encode(['success' => true]);
    }
    // Все события
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $events = $manager->getAllEvents();
        echo json_encode(['success' => true, 'events' => $events]);
    }
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Bad request']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}