<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;

// Подключение к БД и создание таблицы
$pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE DATABASE IF NOT EXISTS patterns");
$pdo->exec("USE patterns");
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100)
    )
");

// Тест

// Массовое получение данных - коллекция
$allUsers = User::getAll();

// Identity Map
$user1 = User::find(1);
$user2 = User::find(1);

// Active Record
$newUser = new User('Test User', 'test@mail.ru');
$newUser->save();

// Вывод результатов
echo "Количество пользователей: " . $allUsers->count() . "\n";
echo "Identity Map работает: " . ($user1 === $user2 ? 'Да' : 'Нет') . "\n";
echo "Новый пользователь ID: " . $newUser->getId() . "\n";