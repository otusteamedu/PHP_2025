<?php
$envFile = dirname(__DIR__) . '/.env';
if (!file_exists($envFile)) {
  die("Файл .env не найден!");
}

$envVariables = parse_ini_file($envFile);

$requiredVariables = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];
foreach ($requiredVariables as $variable) {
  if (!isset($envVariables[$variable])) {
    die("Переменная $variable не найдена в .env!");
  }
}

try {
  $dsn = "pgsql:host={$envVariables['DB_HOST']};port={$envVariables['DB_PORT']};dbname={$envVariables['DB_NAME']}";
  $pdo = new PDO($dsn, $envVariables['DB_USER'], $envVariables['DB_PASSWORD']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Отключаем проверку внешних ключей для безопасного удаления
$pdo->exec('SET session_replication_role = replica;');

// Удаляем данные из таблиц в правильном порядке (от зависимых к родительским)
$tables = ['Ticket', 'Client', 'Session', 'Seat', 'Hall', 'Movie', 'Cinema'];

$startTime = microtime(true);

foreach ($tables as $table) {
  $rowsDeleted = $pdo->exec("DELETE FROM $table");
  echo "Удалено $rowsDeleted записей из таблицы $table\n";
}

// Включаем проверку внешних ключей обратно
$pdo->exec('SET session_replication_role = DEFAULT;');

$endTime = microtime(true);
$executionTime = $endTime - $startTime;

echo "Очистка базы данных завершена!\n";
echo "Общее время выполнения: " . round($executionTime, 2) . " секунд\n";