<?php
$host = 'postgres'; // Имя сервиса из docker-compose
$db   = getenv('POSTGRES_DB') ?: 'hw1_database';
$user = getenv('POSTGRES_USER') ?: 'otus-user';
$pass = getenv('POSTGRES_PASSWORD') ?: 'otus-password';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);

    echo "✅ УСПЕХ: PHP подключился к Postgres!\n";
    echo "Версия сервера: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    
} catch (PDOException $e) {
    echo "❌ ОШИБКА ПОДКЛЮЧЕНИЯ: " . $e->getMessage() . "\n";
}
