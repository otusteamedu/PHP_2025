<?php

/**
 * Пример конфигурационного файла для подключения к базе данных
 * Скопируйте этот файл в config.php и укажите свои настройки
 */

return [
    'host' => 'localhost',
    'dbname' => 'test_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
