<?php
require __DIR__ . '/vendor/autoload.php';

use Achemakin\OtusPhp\Hello;

echo Hello::hi();
echo "<br><hr><br>";

$strProcessor = new Achemakin\TestComposerPackage\StringProcessor();
$testString = "Тестовая строка";
echo "Длина строки '$testString': " . $strProcessor->getLength($testString);;
echo "<br><hr><br>";

testMemcached();
echo "<br><hr><br>";

testRedis();
echo "<br><hr><br>";

testPostgres();

function testMemcached() {
    echo "Тестирование Memcached...<br>";
    // Подключение к Memcached
    $memcached = new Memcached();
    $memcached->addServer('memcached', 11211);

    // Проверка соединения
    if ($memcached->getVersion() === false) {
        die('Не удалось подключиться к Memcached');
    }

    echo "Подключение к Memcached успешно! <br>";

    // Установка значения
    $key = 'test_key';
    $value = 'Hello from Memcached!';
    $memcached->set($key, $value, 3600);
    echo "Установлено: $key = $value <br>";

    // Получение значения
    $result = $memcached->get($key);
    echo "Получено: $key = $result <br>";

    // Работа с массивом
    $memcached->set('user_data', ['name' => 'John', 'age' => 30], 3600);
    $userData = $memcached->get('user_data');
    echo "Данные пользователя: " . print_r($userData, true) . "<br>";

    // Счетчик
    $memcached->set('counter', 0);
    $memcached->increment('counter', 5);
    echo "Счетчик: " . $memcached->get('counter') . "<br>";

    // Статистика
    echo "Статистика Memcached: <br>";
    print_r($memcached->getStats());
}

function testRedis() {
    echo "Тестирование Redis...<br>";
    // Подключение к Redis
    $redis = new Redis();
    $redis->connect('redis', 6379);

    // Проверка соединения
    try {
        $redis->ping();
        echo "Подключение к Redis успешно!<br>";
    } catch (Exception $e) {
        die('Не удалось подключиться к Redis: ' . $e->getMessage());
    }

    // Установка значения
    $key = 'test_key';
    $value = 'Hello from Redis!';
    $redis->set($key, $value);
    echo "Установлено: $key = $value<br>";

    // Получение значения
    $result = $redis->get($key);
    echo "Получено: $key = $result<br>";

    // Работа с массивом (JSON)
    $userData = ['name' => 'Jane', 'age' => 25];
    $redis->set('user_data', json_encode($userData));
    $storedData = json_decode($redis->get('user_data'), true);
    echo "Данные пользователя: " . print_r($storedData, true) . "<br>";

    // Счетчик
    $redis->set('counter', 0);
    $redis->incr('counter');
    $redis->incrBy('counter', 5);
    echo "Счетчик: " . $redis->get('counter') . "<br>";

    // Работа со списком
    $redis->rPush('my_list', 'item1');
    $redis->rPush('my_list', 'item2');
    $redis->rPush('my_list', 'item3');
    echo "Список: " . print_r($redis->lRange('my_list', 0, -1), true) . "<br>";

    // Информация о сервере
    echo "Версия Redis: " . $redis->info('server')['redis_version'] . "<br>";
}

function testPostgres() {
    echo "Тестирование PostgreSQL...<br>";
    
    try {
        // Подключение к PostgreSQL
        $host = getenv('POSTGRES_HOST');
        $port = getenv('POSTGRES_PORT');
        $db   = getenv('POSTGRES_DB');
        $user = getenv('POSTGRES_USER');
        $pass = getenv('POSTGRES_PASSWORD');
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Подключение к PostgreSQL успешно!<br>";

        // Создание таблицы
        $pdo->exec("DROP TABLE IF EXISTS users");
        $pdo->exec("CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            age INTEGER
        )");
        echo "Таблица 'users' создана<br>";

        // Вставка данных
        $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
        $stmt->execute(['Alice', 'alice@example.com', 28]);
        $stmt->execute(['Bob', 'bob@example.com', 32]);
        $stmt->execute(['Charlie', 'charlie@example.com', 25]);
        echo "Добавлено 3 пользователя<br>";

        // Получение данных
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Список пользователей:<br>";
        echo "<pre>" . print_r($users, true) . "</pre>";

        // Обновление данных
        $stmt = $pdo->prepare("UPDATE users SET age = ? WHERE name = ?");
        $stmt->execute([33, 'Bob']);
        echo "Возраст Bob обновлен<br>";

        // Подсчет записей
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "Всего пользователей: $count<br>";

        // Версия PostgreSQL
        $version = $pdo->query("SELECT version()")->fetchColumn();
        echo "Версия PostgreSQL: " . explode(' ', $version)[1] . "<br>";

    } catch (PDOException $e) {
        die('Ошибка подключения к PostgreSQL: ' . $e->getMessage());
    }
}