<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DMapper\UserMapper;

// Создаем подключение к БД
$pdo = new PDO('pgsql:host=postgres;dbname=test', 'pguser', 'pgsecret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Создаем маппер
$userMapper = new UserMapper($pdo);

// Получаем всех пользователей (коллекция)
$users = $userMapper->findAll();

foreach ($users as $user) {
	echo $user->getId() . "\n";
	echo $user->getName() . "\n";
	echo $user->getEmail() . ";<br><br>";
}