<?php
$host = 'db';
$port = $_ENV['POSTGRES_PORT']?:5432;
$dbname = $_ENV['POSTGRES_DB']?:'mydb';
$user = $_ENV['POSTGRES_USER']?:'userTest';
$password = $_ENV['POSTGRES_PASSWORD']?:'eGOSF7Uk_L';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo 'Подключение к PostgreSQL успешно!<br>';
            $result = $pdo->query('SELECT version();')->fetch();
        echo 'PostgreSQL version: ' . $result['version'] . "<br>";

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL
        )
    ');

    $pdo->exec("DELETE FROM users");

    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
    $users = [
        ['name' => 'Alice', 'email' => 'alice@example.com'],
        ['name' => 'Bob',   'email' => 'bob@example.com'],
        ['name' => 'Eve',   'email' => 'eve@example.com'],
    ];
    foreach ($users as $userData) {
        $stmt->execute($userData);
    }
?>
    <p>Данные добавлены!</p>
    <?php

    $query = $pdo->query('SELECT * FROM users ORDER BY id');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>📋 Список пользователей:</h3>
    <table border='1' cellpadding='6' cellspacing='0'>
         <tr>
             <th>ID</th>
             <th>Имя</th>
             <th>Email</th>
         </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?=$row['name'] ?></td>
                <td><?= $row['email'] ?></td>
            </tr>
        <?php endforeach ?>

    </table>
    <?php
    } catch (PDOException $e) {
        echo 'Ошибка подключения: ' . $e->getMessage() . "</br>";
    }