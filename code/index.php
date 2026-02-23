<?php

// Настройки подключения к базе данных
$servername = "db"; // Имя сервиса из docker-compose
$username = "user"; // Имя пользователя, указанное в docker-compose
$password = "user_password"; // Пароль, указанный в docker-compose
$dbname = "my_database"; // Имя базы данных, указанное в docker-compose

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Выполнение простого запроса
$sql = "SELECT DATABASE() AS current_database";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Вывод текущей базы данных
    $row = $result->fetch_assoc();
    echo "Current database: " . $row['current_database'];
} else {
    echo "No results found.";
}

// Закрытие подключения
$conn->close();



phpinfo();
