<?php

$envFile = dirname(__DIR__) . '/.env';
if (!file_exists($envFile)) {
  die("Файл .env не найден!");
}

$envVariables = parse_ini_file($envFile);

$requiredVariables = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'GENERATE_ROWS'];
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
