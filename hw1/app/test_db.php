<?php
$host = 'db';
$db   = 'app';
$user = 'appuser';
$pass = 'secret';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ Подключились к БД MySQL<br>";

    $stmt = $pdo->query("SELECT NOW()");
    echo "Current time from DB: " . $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "❌ DB connection failed: " . $e->getMessage();
}
