<?php

$redis = new RedisCluster(null, [
    'redis-node-1:6379',
    'redis-node-2:6379',
    'redis-node-3:6379',
    'redis-node-4:6379',
    'redis-node-5:6379',
    'redis-node-6:6379',
]);

session_set_save_handler(
    function ($path, $name) {
        return true;
    },
    function () {
        return true;
    },
    function ($id) use ($redis) {
        $sessionData = $redis->get($id);
        return $sessionData ? $sessionData : '';
    },
    function ($id, $data) use ($redis) {
        return $redis->set($id, $data);
    },
    function ($id) use ($redis) {
        return $redis->del($id);
    },
    function ($max_lifetime) {
        return true;
    }
);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Запуск сессии
    session_start();

    // Пример записи в сессию
    if (!isset($_SESSION['views'])) {
        $_SESSION['views'] = 0;
    }
    $_SESSION['views']++;

    // Вывод информации о сессии
    echo "Количество просмотров: " . $_SESSION['views'];

    // Закрытие сессии
    session_write_close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['string'])) {
        http_response_code(400);
        echo "String is empty!";
        exit;
    }

    $string = $_POST['string'];
    $openCount = 0;
    foreach (str_split($string) as $char) {
        if ($char === '(') {
            $openCount++;
        } elseif ($char === ')') {
            $openCount--;
        }
        if ($openCount < 0) {
            http_response_code(400);
            echo "Unbalanced parentheses!";
            exit;
        }
    }

    if ($openCount !== 0) {
        http_response_code(400);
        echo "Unbalanced parentheses!";
    } else {
        http_response_code(200);
        echo "String is valid!";
    }
}